<?php

namespace app\commands\seeds;

use Yii;
use yii\console\Controller;

class EventoSeedController extends Controller
{
    public function actionRun()
    {
        $existe = Yii::$app->db->createCommand("
            SELECT id FROM evento 
            WHERE nome = 'Abertura Oficial do Trote'
        ")->queryOne();

        if (!$existe) {

            Yii::$app->db->createCommand()->insert('evento', [
                'trote_id' => 1,
                'nome' => 'Abertura Oficial do Trote',
                'descricao' => 'Evento de abertura oficial do trote solidário.',
                'ativo' => 1,
                'data_evento' => '2026-03-12',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ])->execute();

            echo "Evento seed criado com sucesso.\n";
        } else {
            echo "Evento já existe.\n";
        }
    }
}
