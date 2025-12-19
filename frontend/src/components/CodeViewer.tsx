import React from 'react';

interface CodeViewerProps {
  code: string;
  holes: {
    id: string;
    label: string;
    options: { id: number; code: string }[];
  }[];
  selectedAnswers: number[];
}

export function CodeViewer({ code, holes, selectedAnswers }: CodeViewerProps) {
  // コードを行ごとに分割
  const lines = code.split('\n');

  // 穴マーカーを見つけてハイライト表示
  const renderLine = (line: string, lineIndex: number) => {
    // __HOLE_N__ パターンを探す
    const parts: React.ReactNode[] = [];
    let lastIndex = 0;
    const regex = /__HOLE_(\d+)__/g;
    let match;

    while ((match = regex.exec(line)) !== null) {
      // マーカーの前のテキスト
      if (match.index > lastIndex) {
        parts.push(
          <span key={`text-${lineIndex}-${lastIndex}`}>
            {line.slice(lastIndex, match.index)}
          </span>
        );
      }

      // マーカー部分
      const holeIndex = parseInt(match[1], 10) - 1;
      const hole = holes[holeIndex];
      const selectedId = selectedAnswers[holeIndex];
      const selectedOption = hole?.options.find(o => o.id === selectedId);

      parts.push(
        <span key={`hole-${lineIndex}-${match.index}`} className="code-hole">
          {selectedOption ? selectedOption.code : `[[ ${hole?.label || match[0]} ]]`}
        </span>
      );

      lastIndex = match.index + match[0].length;
    }

    // 残りのテキスト
    if (lastIndex < line.length) {
      parts.push(
        <span key={`text-${lineIndex}-${lastIndex}`}>
          {line.slice(lastIndex)}
        </span>
      );
    }

    if (parts.length === 0) {
      return line || '\u00A0'; // 空行の場合は non-breaking space
    }

    return parts;
  };

  return (
    <div className="code-block">
      <pre className="m-0">
        <code>
          {lines.map((line, index) => (
            <div key={index} className="flex">
              <span className="text-slate-500 select-none w-8 text-right pr-4">
                {index + 1}
              </span>
              <span>{renderLine(line, index)}</span>
            </div>
          ))}
        </code>
      </pre>
    </div>
  );
}

export default CodeViewer;
