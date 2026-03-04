<?php

use yii\db\Migration;

class m260213_144727_create_trote_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%trote}}', [
            'id'=> $this->primaryKey(),
            'titulo' => $this->string()->notNull(),
            'numero_edicao' => $this->integer()->notNull(),
            'ano' => $this->integer()->notNull(),
            'descricao' => $this->text(),
            'status' => $this->string(20)->notNull()->defaultValue('rascunho'),
            'data_inicio' => $this->date(),   
            'data_fim' => $this->date(),
            //'ativo' => $this->boolean()->notNull()->defaultValue(1)->comment('1 = ativo, 0 = inativo'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // UNIQUE composta: numero_edicao + ano
        $this->createIndex(
            'idx-trote-edicao-ano',
            '{{%trote}}',
            ['numero_edicao', 'ano'],
            true
        );

        // índice para status
        $this->createIndex(
            'idx-trote-status',
            '{{%trote}}',
            'status'
        );

        // índice para ativo
        $this->createIndex(
            'idx-trote-ativo',
            '{{%trote}}',
            'ativo'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%trote}}');
    }
}
