<?php

use yii\db\Migration;

class m260327_180729_create_universidade_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%universidade}}', [
            'id'                   => $this->primaryKey(),
            'nome'                 => $this->string(255),
            'cidade'               => $this->string(255),
            'uf'                   => $this->string(2),
            'icon'                 => $this->string(255),
            'link_doacao_alimento' => $this->string(255),
            'ativo'                => $this->boolean(),
            'created_at'           => $this->dateTime(),
            'updated_at'           => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%universidade}}');
    }
}
