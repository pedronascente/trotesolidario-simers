<?php

use yii\db\Migration;

class m260226_212455_create_table_tipo_doacao extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tipo_doacao}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(150)->notNull(),
            'descricao' => $this->string(255),
            'carga_horaria' => $this->integer()->comment('Carga horária em horas'),
            'pontuacao_ranking' => $this->integer()->comment('Pontuação para ranking'),
            'ativo' => $this->boolean()->notNull()->defaultValue(1)->comment('1 = ativo, 0 = inativo'),
        ]);

        // Índice para filtro por nome
        $this->createIndex(
            'idx-tipo_doacao-nome',
            '{{%tipo_doacao}}',
            'nome'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%tipo_doacao}}');
    }
}
