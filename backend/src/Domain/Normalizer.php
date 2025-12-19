<?php

declare(strict_types=1);

namespace LittleCoder\Domain;

/**
 * 出力正規化
 */
class Normalizer
{
    /**
     * 出力を正規化して比較可能にする
     * 
     * @param string $output 出力文字列
     * @return string 正規化された文字列
     */
    public function normalize(string $output): string
    {
        // 1. 改行コードを LF に統一
        $normalized = str_replace(["\r\n", "\r"], "\n", $output);

        // 2. 各行の末尾スペースを除去
        $lines = explode("\n", $normalized);
        $lines = array_map('rtrim', $lines);

        // 3. 末尾の空行を除去（最後の改行は1つだけ残す）
        while (count($lines) > 0 && $lines[count($lines) - 1] === '') {
            array_pop($lines);
        }

        // 4. 最後に改行を1つ追加
        return implode("\n", $lines) . "\n";
    }

    /**
     * 2つの出力を比較
     * 
     * @param string $expected 期待出力
     * @param string $actual 実際の出力
     * @return bool 一致するかどうか
     */
    public function compare(string $expected, string $actual): bool
    {
        return $this->normalize($expected) === $this->normalize($actual);
    }

    /**
     * 差分を可視化（デバッグ用）
     * 
     * @param string $expected 期待出力
     * @param string $actual 実際の出力
     * @return array{match: bool, expected: string, actual: string, diff: string[]}
     */
    public function diff(string $expected, string $actual): array
    {
        $normExpected = $this->normalize($expected);
        $normActual = $this->normalize($actual);

        $expLines = explode("\n", $normExpected);
        $actLines = explode("\n", $normActual);

        $diff = [];
        $maxLines = max(count($expLines), count($actLines));

        for ($i = 0; $i < $maxLines; $i++) {
            $expLine = $expLines[$i] ?? '<missing>';
            $actLine = $actLines[$i] ?? '<missing>';

            if ($expLine !== $actLine) {
                $diff[] = "Line {$i}: expected '{$expLine}', got '{$actLine}'";
            }
        }

        return [
            'match' => $normExpected === $normActual,
            'expected' => $normExpected,
            'actual' => $normActual,
            'diff' => $diff,
        ];
    }
}
