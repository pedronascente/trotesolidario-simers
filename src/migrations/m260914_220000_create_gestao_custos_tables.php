<?php

use yii\db\Migration;

class m260914_220000_create_gestao_custos_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%categoria_custo}}', [
            'id' => $this->primaryKey(),
            'trote_id' => $this->integer()->notNull(),
            'categoria' => $this->string(120)->notNull(),
            'valor_previsto' => $this->decimal(12, 2)->notNull(),
            'descricao' => $this->string(500)->notNull(),
            'observacao' => $this->string(500),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->createIndex('uq-categoria_custo-trote-categoria', '{{%categoria_custo}}', ['trote_id', 'categoria'], true);
        $this->addForeignKey('fk-categoria_custo-trote', '{{%categoria_custo}}', 'trote_id', '{{%trote}}', 'id', 'RESTRICT', 'CASCADE');

        $this->createTable('{{%distribuicao_custo}}', [
            'id' => $this->primaryKey(),
            'categoria_custo_id' => $this->integer()->notNull(),
            'universidade_id' => $this->integer()->notNull(),
            'valor_previsto' => $this->decimal(12, 2)->notNull(),
            'observacao' => $this->string(500),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->createIndex('uq-distribuicao_custo-categoria-universidade', '{{%distribuicao_custo}}', ['categoria_custo_id', 'universidade_id'], true);
        $this->createIndex('idx-distribuicao_custo-universidade', '{{%distribuicao_custo}}', 'universidade_id');
        $this->addForeignKey('fk-distribuicao_custo-categoria', '{{%distribuicao_custo}}', 'categoria_custo_id', '{{%categoria_custo}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-distribuicao_custo-universidade', '{{%distribuicao_custo}}', 'universidade_id', '{{%universidade}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%distribuicao_custo}}');
        $this->dropTable('{{%categoria_custo}}');
    }
}
