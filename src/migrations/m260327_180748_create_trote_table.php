<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trote}}`.
 */
class m260327_180748_create_trote_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%trote}}', [
            'id'            => $this->primaryKey(),
            'titulo'        => $this->string(255),
            'descricao'     => $this->text(),
            'edicao'        => $this->string(10)->notNull()->unique(),
            'status'        => "ENUM('rascunho','ativo','encerrado') NOT NULL DEFAULT 'rascunho'",
            'data_inicio'   => $this->date(),
            'data_fim'      => $this->date(),
            'created_at'    => $this->dateTime(),
            'updated_at'    => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%trote}}');
    }
}
