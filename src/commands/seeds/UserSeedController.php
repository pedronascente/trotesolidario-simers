<?php

namespace app\commands\seeds;

use Yii;
use yii\console\Controller;

class UserSeedController extends Controller
{
    public function actionRun()
    {
        $exists = (new \yii\db\Query())
            ->from('user')
            ->where(['email' => 'pedro.jardim@simers.org.br'])
            ->exists();

        if ($exists) {
            echo "Usuário já existe\n";
            return;
        }

        Yii::$app->db->createCommand()->insert('user', [

            'name' => 'Pedro Jardim',
            'username' => 'pedro',
            'email' => 'pedro.jardim@simers.org.br',
            'passwordHash' => '$2y$13$WP762A8RrNXI5CSwdGYTUOoIxzMc.7qAGdTavy0zpxPIMsowTaWTe',
            'status' => 1,
            'administrator' => 1,
            'cpf' => '82146942053',
            'estudante' => 'Não',
            'authKey' => '6vPQC66huM1m8J7yphbm32_GLH0nHZlG',
            'created_at' => new \yii\db\Expression('NOW()'),
        ])->execute();

        echo "Usuário admin criado\n";
    }
}