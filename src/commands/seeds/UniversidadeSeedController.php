<?php

namespace app\commands\seeds;

use Yii;
use yii\console\Controller;

class UniversidadeSeedController extends Controller
{
    public function actionRun()
    {
        $universidades = [
            [
                'nome' => 'UFRGS - Universidade Federal do RS',
                'cidade' => 'Porto Alegre',
                'uf' => 'RS',
                'icon' => 'uni_69b1c87f46b03.jpg',
                'link_doacao_alimento' => 'https://www.doealimentos.com.br/Inicial/referrer=unisinos25s',
                'ativo' => 1,
                'created_at' => '2026-03-11 19:54:39',
                'updated_at' => '2026-03-11 19:54:39',
            ]
        ];

        foreach ($universidades as $universidade) {

            $existe = Yii::$app->db->createCommand("
                SELECT id FROM universidade WHERE nome = :nome
            ")
                ->bindValue(':nome', $universidade['nome'])
                ->queryOne();

            if (!$existe) {

                Yii::$app->db->createCommand()->insert('universidade', $universidade)->execute();

                echo "Seed criado: {$universidade['nome']}\n";
            } else {

                echo "Universidade já existe: {$universidade['nome']}\n";
            }
        }

        echo "Seeds de universidades finalizados.\n";
    }
}
