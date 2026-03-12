<?php

namespace app\commands\seeds;

use Yii;
use yii\console\Controller;

class TroteSeedController extends Controller
{
    public function actionRun()
    {
        Yii::$app->db->createCommand()->insert('trote', [
            'titulo' => 'Trote solidario 1/2026',
            'numero_edicao' => 1,
            'ano' => 2026,
            'descricao' => 'Trote solidário voltado para arrecadação de doações e ações sociais.',
            'status' => 'ativo',
            'data_inicio' => '2026-03-11',
            'data_fim' => '2026-03-28',
            'ativo' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ])->execute();

        echo "Trote seed criado com sucesso.\n";
    }
}
