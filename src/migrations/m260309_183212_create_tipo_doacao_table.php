<?php

use yii\db\Migration;

class m260309_183212_create_tipo_doacao_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tipo_doacao}}', [
            'id'                => $this->primaryKey(),
            'nome'              => $this->string(150),
            'descricao'         => $this->string(255),
            'carga_horaria'     => $this->integer(),
            'pontuacao_ranking' => $this->integer(),
            'ativo'             => $this->boolean(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%tipo_doacao}}');
    }
}