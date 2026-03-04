<?php

use yii\db\Migration;

class m260212_192645_create_universidade_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%universidade}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(255)->notNull(),
            'cidade' => $this->string(255)->notNull(),
            'uf'=> $this->string(2)->notNull(),
            'icon' => $this->string(255),
            'link_doacao_alimento' => $this->string(255)->notNull(),
            'ativo' => $this->boolean()->notNull()->defaultValue(1)->comment('1 = ativo, 0 = inativo'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Índices úteis
        $this->createIndex(
            'idx_universidade_nome',
            '{{%universidade}}',
            'nome'
        );

        $this->createIndex(
            'idx_universidade_uf',
            '{{%universidade}}',
            'uf'
        );
    }

    public function safeDown() {
        $this->dropTable('{{%universidade}}');
    }
}
