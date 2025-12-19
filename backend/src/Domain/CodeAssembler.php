<?php

declare(strict_types=1);

namespace LittleCoder\Domain;

/**
 * 穴埋めコード組み立て
 */
class CodeAssembler
{
    /**
     * テンプレートと回答から完成コードを生成
     * 
     * @param string $templateCode テンプレートコード（穴マーカー込み）
     * @param array $holes 穴埋め情報
     * @param array $answers 選択されたオプションIDの配列（穴の順番に対応）
     * @return string 完成したコード
     * @throws \InvalidArgumentException 無効な回答の場合
     */
    public function assemble(string $templateCode, array $holes, array $answers): string
    {
        // 回答数のバリデーション
        if (count($answers) !== count($holes)) {
            throw new \InvalidArgumentException(
                sprintf('Answer count mismatch: expected %d, got %d', count($holes), count($answers))
            );
        }

        $code = $templateCode;

        foreach ($holes as $index => $hole) {
            $holeId = $hole['id'];
            $answerId = $answers[$index];

            // オプションを検索
            $selectedOption = null;
            foreach ($hole['options'] as $option) {
                if ($option['id'] === $answerId) {
                    $selectedOption = $option;
                    break;
                }
            }

            if ($selectedOption === null) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid answer ID %d for hole %s', $answerId, $holeId)
                );
            }

            // マーカーを置換
            $marker = "__" . $holeId . "__";
            if (strpos($code, $marker) === false) {
                throw new \InvalidArgumentException(
                    sprintf('Hole marker not found in template: %s', $marker)
                );
            }

            $code = str_replace($marker, $selectedOption['code'], $code);
        }

        // 未置換のマーカーがないかチェック
        if (preg_match('/__HOLE_\d+__/', $code, $matches)) {
            throw new \InvalidArgumentException(
                sprintf('Unresolved hole marker: %s', $matches[0])
            );
        }

        return $code;
    }

    /**
     * テンプレートから穴マーカーを抽出
     * 
     * @param string $templateCode テンプレートコード
     * @return string[] 穴マーカーの配列
     */
    public function extractMarkers(string $templateCode): array
    {
        preg_match_all('/__HOLE_(\d+)__/', $templateCode, $matches);
        return $matches[0] ?? [];
    }

    /**
     * 表示用のテンプレートを生成（マーカーをハイライト表示用に変換）
     * 
     * @param string $templateCode テンプレートコード
     * @return string 表示用コード
     */
    public function formatForDisplay(string $templateCode): string
    {
        // __HOLE_1__ を [[ HOLE_1 ]] に変換（表示用）
        return preg_replace('/__HOLE_(\d+)__/', '[[ HOLE_$1 ]]', $templateCode);
    }
}
