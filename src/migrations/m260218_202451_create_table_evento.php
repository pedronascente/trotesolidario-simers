<?php

use yii\db\Migration;

class m260218_202451_create_table_evento extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%evento}}', [
            'id' => $this->primaryKey(),
            'trote_id' => $this->integer()->notNull(),
            'nome' => $this->string(255)->notNull(),
            'descricao' => $this->string(255),
            'ativo' => $this->boolean()->notNull()->defaultValue(1),
            'data_evento' => $this->date()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // 🔎 Índice para FK (boa prática performance)
        $this->createIndex(
            'idx-evento-trote_id',
            '{{%evento}}',
            'trote_id'
        );

        // 🔗 Foreign Key
        $this->addForeignKey(
            'fk-evento-trote_id',
            '{{%evento}}',
            'trote_id',
            '{{%trote}}',
            'id',
            'CASCADE',   // se deletar trote, deleta eventos
            'RESTRICT'   // não permite alterar id do trote se houver eventos
        );
    }

    public function safeDown()
    {
        // Remove FK primeiro
        $this->dropForeignKey(
            'fk-evento-trote_id',
            '{{%evento}}'
        );

        // Remove índice
        $this->dropIndex(
            'idx-evento-trote_id',
            '{{%evento}}'
        );

        // Dropa tabela
        $this->dropTable('{{%evento}}');
    }
}
