<?php

namespace app\commands\seeds;

use Yii;

class UserSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'user';
    }

    public function run()
    {
        $now = $this->now();

        $this->upsertRows('{{%user}}', [
            [
                'id' => 1,
                'nome' => 'Administrador Geral',
                'email' => 'admin@trotesolidario.test',
                'username' => 'admin',
                'cpf' => '82146942053',
                'password_hash' => Yii::$app->security->generatePasswordHash('%6Pdrx92399880'),
                'role' => 'admin',
                'authKey' => 'seed-auth-admin',
                'password_reset_token' => null,
                'status' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'nome' => 'Ana Participante',
                'email' => 'ana@trotesolidario.test',
                'username' => 'ana.participante',
                'cpf' => '11144477735',
                'password_hash' => Yii::$app->security->generatePasswordHash('Participante@123'),
                'role' => 'participante',
                'authKey' => 'seed-auth-ana',
                'password_reset_token' => null,
                'status' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'nome' => 'Bruno Participante',
                'email' => 'bruno@trotesolidario.test',
                'username' => 'bruno.participante',
                'cpf' => '12345678909',
                'password_hash' => Yii::$app->security->generatePasswordHash('Participante@123'),
                'role' => 'participante',
                'authKey' => 'seed-auth-bruno',
                'password_reset_token' => null,
                'status' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'nome' => 'Carla Comissao',
                'email' => 'carla@trotesolidario.test',
                'username' => 'carla.comissao',
                'cpf' => '98765432100',
                'password_hash' => Yii::$app->security->generatePasswordHash('Participante@123'),
                'role' => 'participante',
                'authKey' => 'seed-auth-carla',
                'password_reset_token' => null,
                'status' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}