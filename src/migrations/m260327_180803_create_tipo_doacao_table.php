<?php

use yii\db\Migration;

class m260327_180803_create_tipo_doacao_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tipo_doacao}}', [
            'id'                => $this->primaryKey(),
            'nome'              => $this->string(100)->notNull(),
            'descricao'         => $this->text(),
            'carga_horaria'     => $this->integer()->notNull()->defaultValue(0),
            'pontuacao_ranking' => $this->integer()->notNull()->defaultValue(0),
            'ativo'             => $this->boolean()->notNull()->defaultValue(true),
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
