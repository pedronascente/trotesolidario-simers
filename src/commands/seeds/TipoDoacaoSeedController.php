<?php

namespace app\commands\seeds;

use Yii;
use yii\console\Controller;

class TipoDoacaoSeedController extends Controller
{
    public function actionRun()
    {
        $tipos = [
            [
                'nome' => 'Doação de Alimentos',
                'descricao' => 'Arrecadação de alimentos não perecíveis.',
                'carga_horaria' => 4,
                'pontuacao_ranking' => 20,
                'ativo' => 1
            ],
            [
                'nome' => 'Doação de Roupas',
                'descricao' => 'Arrecadação de roupas para doação.',
                'carga_horaria' => 8,
                'pontuacao_ranking' => 34,
                'ativo' => 1
            ],
            [
                'nome' => 'Doação de Sangue',
                'descricao' => 'Doação realizada em hemocentros parceiros.',
                'carga_horaria' => 12,
                'pontuacao_ranking' => 34,
                'ativo' => 1
            ],
        ];

        foreach ($tipos as $tipo) {

            $existe = Yii::$app->db->createCommand("
                SELECT id FROM tipo_doacao WHERE nome = :nome
            ")
                ->bindValue(':nome', $tipo['nome'])
                ->queryOne();

            if (!$existe) {

                Yii::$app->db->createCommand()->insert('tipo_doacao', $tipo)->execute();

                echo "Seed criado: {$tipo['nome']}\n";
            } else {

                echo "Já existe: {$tipo['nome']}\n";
            }
        }

        echo "Seeds de tipo de doação finalizados.\n";
    }
}
