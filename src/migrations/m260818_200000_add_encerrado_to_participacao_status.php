<?php

use yii\db\Migration;

class m260818_200000_add_encerrado_to_participacao_status extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(
            'participacao',
            'status',
            "ENUM('ativo','cancelado','encerrado') NOT NULL DEFAULT 'ativo'"
        );
    }

    public function safeDown()
    {
        $this->update(
            'participacao',
            ['status' => 'cancelado'],
            ['status' => 'encerrado']
        );

        $this->alterColumn(
            'participacao',
            'status',
            "ENUM('ativo','cancelado') NOT NULL DEFAULT 'ativo'"
        );
    }
}
