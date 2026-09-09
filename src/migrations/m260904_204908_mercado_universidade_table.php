<?php

use yii\db\Migration;

class m260904_204908_mercado_universidade_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%mercado_universidade}}', [
            'id' => $this->primaryKey(),

            'mercado_id' => $this->integer()->notNull(),

            'universidade_id' => $this->integer()->notNull(),

            'created_at' => $this->integer()->null(),
        ]);

        // FK para mercado
        $this->addForeignKey(
            'fk-mercado_universidade-mercado_id',
            '{{%mercado_universidade}}',
            'mercado_id',
            '{{%mercado_parceiro}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // FK para universidade
        $this->addForeignKey(
            'fk-mercado_universidade-universidade_id',
            '{{%mercado_universidade}}',
            'universidade_id',
            '{{%universidade}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Impede o mesmo mercado de ser vinculado
        // duas vezes à mesma universidade
        $this->createIndex(
            'idx-mercado_universidade-unico',
            '{{%mercado_universidade}}',
            ['mercado_id', 'universidade_id'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-mercado_universidade-mercado_id',
            '{{%mercado_universidade}}'
        );

        $this->dropForeignKey(
            'fk-mercado_universidade-universidade_id',
            '{{%mercado_universidade}}'
        );

        $this->dropTable('{{%mercado_universidade}}');
    }
}