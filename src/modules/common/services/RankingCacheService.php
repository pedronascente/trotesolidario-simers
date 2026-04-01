<?php

namespace app\modules\common\services;

use app\modules\common\models\Doacao;
use app\modules\common\models\Participacao;
use app\modules\common\models\RankingCache;
use app\modules\common\models\Trote;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class RankingCacheService implements RankingCacheServiceInterface
{
    public function findTrotes(): array
    {
        $trotes = Trote::find()->orderBy(['titulo' => SORT_ASC, 'edicao' => SORT_DESC])->all();

        return ArrayHelper::map($trotes, 'id', static function (Trote $trote) {
            $titulo = $trote->titulo ?: 'Sem titulo';
            $edicao = $trote->edicao ?: 'Sem edicao';
            return $titulo . ' | ' . $edicao;
        });
    }

    public function rebuild(?int $troteId = null): int
    {
        return Yii::$app->db->transaction(function () use ($troteId) {
            $participacoes = Participacao::find()
                ->where(['status' => Participacao::STATUS_ATIVO])
                ->andFilterWhere(['trote_id' => $troteId])
                ->orderBy(['trote_id' => SORT_ASC, 'id' => SORT_ASC])
                ->all();

            if ($troteId === null) {
                RankingCache::deleteAll();
            } else {
                RankingCache::deleteAll(['trote_id' => $troteId]);
            }

            $scoresByTrote = [];
            foreach ($participacoes as $participacao) {
                $score = (new Query())
                    ->from(['d' => 'doacao'])
                    ->innerJoin(['td' => 'tipo_doacao'], 'td.id = d.tipo_doacao_id')
                    ->where([
                        'd.participacao_id' => $participacao->id,
                        'd.status' => Doacao::STATUS_APROVADA,
                    ])
                    ->sum('td.pontuacao_ranking');

                $scoresByTrote[$participacao->trote_id][] = [
                    'participacao_id' => $participacao->id,
                    'trote_id' => $participacao->trote_id,
                    'pontuacao_total' => (int) ($score ?? 0),
                ];
            }

            $rows = [];
            foreach ($scoresByTrote as $entries) {
                usort($entries, static function (array $a, array $b) {
                    if ($a['pontuacao_total'] === $b['pontuacao_total']) {
                        return $a['participacao_id'] <=> $b['participacao_id'];
                    }

                    return $b['pontuacao_total'] <=> $a['pontuacao_total'];
                });

                $lastScore = null;
                $currentPosition = 0;
                foreach ($entries as $index => $entry) {
                    if ($lastScore !== $entry['pontuacao_total']) {
                        $currentPosition = $index + 1;
                        $lastScore = $entry['pontuacao_total'];
                    }

                    $rows[] = [
                        'participacao_id' => $entry['participacao_id'],
                        'trote_id' => $entry['trote_id'],
                        'pontuacao_total' => $entry['pontuacao_total'],
                        'posicao' => $currentPosition,
                        'updated_at' => new Expression('NOW()'),
                    ];
                }
            }

            if (!empty($rows)) {
                Yii::$app->db->createCommand()->batchInsert(
                    RankingCache::tableName(),
                    ['participacao_id', 'trote_id', 'pontuacao_total', 'posicao', 'updated_at'],
                    array_map(static function (array $row) {
                        return [
                            $row['participacao_id'],
                            $row['trote_id'],
                            $row['pontuacao_total'],
                            $row['posicao'],
                            $row['updated_at'],
                        ];
                    }, $rows)
                )->execute();
            }

            return count($rows);
        });
    }

    public function getUniversityRanking(?int $troteId = null): array
    {
        return (new Query())
            ->select([
                'u.id AS universidade_id',
                'u.nome',
                'rc.trote_id',
                'SUM(rc.pontuacao_total) AS pontos',
                'COUNT(DISTINCT rc.participacao_id) AS participantes',
                'MIN(rc.posicao) AS melhor_posicao',
            ])
            ->from(['rc' => 'ranking_cache'])
            ->innerJoin(['p' => 'participacao'], 'p.id = rc.participacao_id')
            ->innerJoin(['u' => 'universidade'], 'u.id = p.universidade_id')
            ->andFilterWhere(['rc.trote_id' => $troteId])
            ->groupBy(['u.id', 'u.nome', 'rc.trote_id'])
            ->orderBy(['pontos' => SORT_DESC, 'melhor_posicao' => SORT_ASC, 'u.nome' => SORT_ASC])
            ->all();
    }
}
