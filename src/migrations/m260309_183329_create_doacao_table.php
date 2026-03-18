<?php

use yii\db\Migration;

class m260309_183329_create_doacao_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%doacao}}', [
            'id' => $this->primaryKey(),
            'trote_id'        => $this->integer()->notNull(),
            'evento_id'       => $this->integer()->notNull(),
            'user_id'         => $this->integer()->notNull(),
            'universidade_id' => $this->integer()->notNull(),
            'tipo_doacao_id'  => $this->integer()->notNull(),
            'comprovante'     => $this->string(),
            'status'          => $this->string(20)->notNull()->defaultValue('pendente'),
            'arquivo'         => $this->string(255),
            'validado_por'    => $this->integer()->null(),
            'validado_em'     => $this->dateTime()->null(),
            'created_at'      => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'      => $this->dateTime()->append('ON UPDATE CURRENT_TIMESTAMP'),
            'aprovar'         => $this->boolean(),
            'observacao' => $this->text(),
        ]);

        // INDEXES

        $this->createIndex('idx-doacao-trote', '{{%doacao}}', 'trote_id');
        $this->createIndex('idx-doacao-evento', '{{%doacao}}', 'evento_id');
        $this->createIndex('idx-doacao-universidade', '{{%doacao}}', 'universidade_id');
        $this->createIndex('idx-doacao-tipo', '{{%doacao}}', 'tipo_doacao_id');

        // REGRA DE NEGÓCIO
        $this->createIndex(
            'uk-doacao-user-trote-tipo',
            '{{%doacao}}',
            ['user_id', 'trote_id', 'tipo_doacao_id'],
            true
        );

        // FOREIGN KEYS

        $this->addForeignKey(
            'fk-doacao-trote',
            '{{%doacao}}',
            'trote_id',
            '{{%trote}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-evento',
            '{{%doacao}}',
            'evento_id',
            '{{%evento}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-user',
            '{{%doacao}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-universidade',
            '{{%doacao}}',
            'universidade_id',
            '{{%universidade}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-tipo',
            '{{%doacao}}',
            'tipo_doacao_id',
            '{{%tipo_doacao}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-doacao-validado',
            '{{%doacao}}',
            'validado_por',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%doacao}}');
    }
}