<?php

use yii\db\Migration;

class m260309_183212_create_tipo_doacao_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tipo_doacao}}', [
            'id'                => $this->primaryKey(),
            'nome'              => $this->string(150)->notNull(),
            'descricao'         => $this->string(255),
            'carga_horaria'     => $this->integer()->notNull(),
            'pontuacao_ranking' => $this->integer()->notNull(),
            'ativo'             => $this->boolean()->defaultValue(1),
        ]);

        // 🔒 UNIQUE no nome
        $this->createIndex(
            'idx-tipo_doacao-nome-unique',
            '{{%tipo_doacao}}',
            'nome',
            true // UNIQUE
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%tipo_doacao}}');
    }
}
