import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

// Parse arguments manually
const args = process.argv.slice(2);

function getArg(flag, defaultValue) {
    const index = args.indexOf(flag);
    if (index !== -1 && index + 1 < args.length) {
        return args[index + 1];
    }
    return defaultValue;
}

const inputArg = getArg('--input', 'ARCHITECTURE.html');
const outDirArg = getArg('--out', 'png_out');
const waitArg = getArg('--wait', '1500');

const INPUT_FILE = path.resolve(process.cwd(), inputArg);
const OUTPUT_DIR = path.resolve(process.cwd(), outDirArg);
const WAIT_MS = parseInt(waitArg, 10);

if (!fs.existsSync(INPUT_FILE)) {
    console.error(`Error: Input file definition '${INPUT_FILE}' does not exist.`);
    process.exit(1);
}

if (!fs.existsSync(OUTPUT_DIR)) {
    fs.mkdirSync(OUTPUT_DIR, { recursive: true });
}

(async () => {
    console.log(`[Start] Exporting Mermaid diagrams from ${inputArg} to ${outDirArg}/`);

    // Launch browser
    const browser = await chromium.launch();
    const context = await browser.newContext({
        viewport: { width: 1280, height: 720 }, // Base viewport
        deviceScaleFactor: 2 // High DPI for better quality in Word
    });
    const page = await context.newPage();

    // Open local file
    const fileUrl = 'file://' + INPUT_FILE;
    console.log(`Loading: ${fileUrl}`);
    await page.goto(fileUrl);
    
    // Wait for rendering
    console.log(`Waiting ${WAIT_MS}ms for Mermaid rendering...`);
    await page.waitForTimeout(WAIT_MS);

    // Get all mermaid containers
    const handles = await page.$$('div.mermaid');
    const count = handles.length;

    if (count === 0) {
        console.error("Error: No 'div.mermaid' elements found in the file.");
        await browser.close();
        process.exit(1);
    }

    console.log(`Found ${count} diagrams. Starting export...`);

    const errors = [];

    for (let i = 0; i < count; i++) {
        const handle = handles[i];
        const index = i + 1;
        const indexStr = String(index).padStart(2, '0');

        // Determine filename from preceding h2
        // We look for previous sibling that is H2.
        const headerText = await handle.evaluate(el => {
            let prev = el.previousElementSibling;
            // Limit search backward
            let limit = 10; 
            while (prev && limit > 0) {
                if (prev.tagName === 'H2') {
                    return prev.textContent;
                }
                prev = prev.previousElementSibling;
                limit--;
            }
            return 'NoTitle';
        });

        const safeTitle = headerText.trim()
            .replace(/[\\/:*?"<>|]/g, '_') // Forbidden chars windows
            .replace(/\s+/g, '_')   // Spaces to underscore
            .replace(/\./g, '');    // Remove dots to avoid extension issues

        const filename = `${indexStr}_${safeTitle}.png`;
        const filepath = path.join(OUTPUT_DIR, filename);

        // Check availability (SVG existence)
        const hasSvg = await handle.$('svg');

        if (hasSvg) {
            try {
                // Ensure visibility
                await handle.scrollIntoViewIfNeeded();
                
                // Screenshot
                // element.screenshot handles sizing automatically to fit element
                await handle.screenshot({ path: filepath });
                console.log(`Saved: ${filename}`);
            } catch (err) {
                console.error(`Error saving ${filename}: ${err.message}`);
                errors.push({ id: index, file: filename, error: err.message });
            }
        } else {
            console.warn(`[Warning] Diagram ${index} has no SVG generated. Attempting fallback...`);
            try {
                // FALLBACK: Extract text and render in clean page with clean mermaid
                const mermaidCode = await handle.innerText();
                if (!mermaidCode.trim()) throw new Error("Empty mermaid content");

                // Reuse browser, new page
                const fbPage = await context.newPage();
                
                // Inject our own mermaid.min.js
                // We installed 'mermaid' via npm, let's use it.
                // Resolving 'mermaid/dist/mermaid.min.js'
                // Since this is ESM script execution, we need to access node_modules manually
                const nodeModulesPath = path.join(process.cwd(), 'node_modules', 'mermaid', 'dist', 'mermaid.min.js');
                let scriptContent = '';
                if (fs.existsSync(nodeModulesPath)) {
                    scriptContent = fs.readFileSync(nodeModulesPath, 'utf8');
                } else {
                    // Fallback to CDN if local not found (unlikely if npm i worked)
                    console.warn("Could not find local mermaid.min.js, using CDN");
                    scriptContent = null; 
                }

                await fbPage.setContent(`
                    <!DOCTYPE html>
                    <html>
                    <head><style>body { background: white; }</style></head>
                    <body>
                        <div class="mermaid">${mermaidCode}</div>
                    </body>
                    </html>
                `);

                if (scriptContent) {
                    await fbPage.addScriptTag({ content: scriptContent });
                } else {
                    await fbPage.addScriptTag({ url: 'https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js' });
                }

                // Initialize and run
                await fbPage.evaluate(async () => {
                    mermaid.initialize({ startOnLoad: true });
                    await mermaid.run();
                });

                // Wait for SVG
                try {
                    await fbPage.waitForSelector('.mermaid svg', { timeout: 5000 });
                } catch (e) {
                    throw new Error("Fallback rendering timed out (no SVG).");
                }

                const fbHandle = await fbPage.$('.mermaid');
                // Adjust viewport to ensure high resolution if needed, though dsf=2 helps
                await fbHandle.screenshot({ path: filepath });
                console.log(`Saved (Fallback): ${filename}`);
                
                await fbPage.close();

            } catch (err) {
                console.error(`Fallback failed for ${filename}: ${err.message}`);
                errors.push({ id: index, file: filename, error: err.message });
            }
        }
    }

    await browser.close();

    if (errors.length > 0) {
        console.log("\n--- Validating Results ---");
        console.log(`Success: ${count - errors.length}/${count}`);
        console.log(`Failures: ${errors.length}`);
        const logPath = path.join(OUTPUT_DIR, 'errors.log');
        fs.writeFileSync(logPath, JSON.stringify(errors, null, 2));
        console.log(`Error details written to ${logPath}`);
    } else {
        console.log(`\nSuccess! All ${count} diagrams exported to ${outDirArg}/`);
    }

})();
