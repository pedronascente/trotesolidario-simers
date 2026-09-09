<?php

use yii\db\Migration;

class m260903_203100_create_cidade_participante_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%cidade_participante}}', [
            'id' => $this->primaryKey(),
            'cidade' => $this->string(255)->notNull(),
            'uf' => $this->string(2)->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->createIndex(
            'ux_cidade_participante_cidade_uf',
            '{{%cidade_participante}}',
            ['cidade', 'uf'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%cidade_participante}}');
    }
}
