<?php

use yii\db\Migration;

class m260309_183317_create_evento_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%evento}}', [
            'id'          => $this->primaryKey(),
            'trote_id'    => $this->integer(),
            'nome'        => $this->string(255),
            'descricao'   => $this->string(255),
            'ativo'       => $this->boolean(),
            'data_evento' => $this->date(),
            'created_at'  => $this->dateTime(),
            'updated_at'  => $this->dateTime(),
        ]);

        $this->createIndex(
            'idx-evento-trote_id',
            'evento',
            'trote_id'
        );

        $this->addForeignKey(
            'fk-evento-trote',
            'evento',
            'trote_id',
            'trote',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-evento-trote', 'evento');
        $this->dropIndex('idx-evento-trote_id', 'evento');
        $this->dropTable('{{%evento}}');
    }
}
