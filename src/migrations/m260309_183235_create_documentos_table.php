<?php

use yii\db\Migration;

class m260309_183235_create_documentos_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%documentos}}', [
            'id'         => $this->primaryKey(),
            'nome'       => $this->string(255),
            'arquivo'    => $this->string(255),
            'tipo'       => $this->string(20),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%documentos}}');
    }
}
