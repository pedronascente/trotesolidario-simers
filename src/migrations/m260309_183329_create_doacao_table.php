<?php

use yii\db\Migration;

class m260309_183329_create_doacao_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%doacao}}', [
            'id'              => $this->primaryKey(),
            'trote_id'        => $this->integer()->notNull(),
            'evento_id'       => $this->integer()->notNull(),
            'user_id'         => $this->integer()->notNull(),
            'universidade_id' => $this->integer()->notNull(),
            'tipo_doacao_id'  => $this->integer()->notNull(),
            'comprovante'     => $this->string(),
            'status'          => "ENUM('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente'",
            'validado_por'    => $this->integer()->null(),
            'validado_em'     => $this->dateTime()->null(),
            'created_at'      => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'observacao'      => $this->text()->null(),
        ]);

        // indexes
        $this->createIndex('idx-doacao-trote_id', 'doacao', 'trote_id');
        $this->createIndex('idx-doacao-evento_id', 'doacao', 'evento_id');
        $this->createIndex('idx-doacao-user_id', 'doacao', 'user_id');
        $this->createIndex('idx-doacao-universidade_id', 'doacao', 'universidade_id');
        $this->createIndex('idx-doacao-tipo_doacao_id', 'doacao', 'tipo_doacao_id');

        // UNIQUE
        $this->createIndex(
            'uk-doacao-user-trote-tipo',
            'doacao',
            ['user_id', 'trote_id', 'tipo_doacao_id'],
            true
        );

        // foreign keys

        $this->addForeignKey(
            'fk-doacao-trote',
            'doacao',
            'trote_id',
            'trote',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-evento',
            'doacao',
            'evento_id',
            'evento',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-user',
            'doacao',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-universidade',
            'doacao',
            'universidade_id',
            'universidade',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-tipo',
            'doacao',
            'tipo_doacao_id',
            'tipo_doacao',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-validado_por',
            'doacao',
            'validado_por',
            'user',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-doacao-validado_por', 'doacao');
        $this->dropForeignKey('fk-doacao-tipo', 'doacao');
        $this->dropForeignKey('fk-doacao-universidade', 'doacao');
        $this->dropForeignKey('fk-doacao-user', 'doacao');
        $this->dropForeignKey('fk-doacao-evento', 'doacao');
        $this->dropForeignKey('fk-doacao-trote', 'doacao');

        $this->dropTable('{{%doacao}}');
    }
}
