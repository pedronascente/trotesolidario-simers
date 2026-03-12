<?php

namespace app\commands\seeds;

use Yii;
use yii\console\Controller;

class DoacaoSeedController extends Controller
{
    public function actionRun()
    {
        $doacoes = [
            [
                'trote_id' => 1,
                'evento_id' => 1,
                'user_id' => 1,
                'universidade_id' => 1,
                'tipo_doacao_id' => 1,
                'comprovante' => null,
                'status' => 'pendente',
                'arquivo' => 'uni_69b1c89d9522b.jpg',
                'validado_por' => null,
                'validado_em' => null,
                'created_at' => '2026-03-11 19:55:09',
                'updated_at' => '2026-03-11 21:23:49',
                'aprovar' => null,
                'observacao' => null,
            ],
            [
                'trote_id' => 1,
                'evento_id' => 1,
                'user_id' => 1,
                'universidade_id' => 1,
                'tipo_doacao_id' => 2,
                'comprovante' => null,
                'status' => 'pendente',
                'arquivo' => 'uni_69b1cace5a044.jpg',
                'validado_por' => null,
                'validado_em' => null,
                'created_at' => '2026-03-11 20:04:30',
                'updated_at' => '2026-03-11 21:23:49',
                'aprovar' => null,
                'observacao' => null,
            ],
            [
                'trote_id' => 1,
                'evento_id' => 1,
                'user_id' => 1,
                'universidade_id' => 1,
                'tipo_doacao_id' => 3,
                'comprovante' => null,
                'status' => 'pendente',
                'arquivo' => 'uni_69b1d0e26c1de.jpg',
                'validado_por' => null,
                'validado_em' => null,
                'created_at' => '2026-03-11 20:30:26',
                'updated_at' => '2026-03-11 21:23:49',
                'aprovar' => null,
                'observacao' => null,
            ],
        ];

        foreach ($doacoes as $doacao) {

            $existe = Yii::$app->db->createCommand("
                SELECT id FROM doacao 
                WHERE user_id = :user 
                AND tipo_doacao_id = :tipo
                AND trote_id = :trote
            ")
                ->bindValues([
                    ':user' => $doacao['user_id'],
                    ':tipo' => $doacao['tipo_doacao_id'],
                    ':trote' => $doacao['trote_id'],
                ])
                ->queryOne();

            if (!$existe) {

                Yii::$app->db->createCommand()->insert('doacao', $doacao)->execute();

                echo "Seed criado para tipo_doacao_id {$doacao['tipo_doacao_id']}\n";
            } else {

                echo "Doação já existe para tipo {$doacao['tipo_doacao_id']}\n";
            }
        }

        echo "Seeds de doação finalizados.\n";
    }
}
