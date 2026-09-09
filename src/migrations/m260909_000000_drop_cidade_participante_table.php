<?php

use yii\db\Migration;

class m260909_000000_drop_cidade_participante_table extends Migration
{
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%cidade_participante}}', true) === null) {
            return;
        }

        $this->dropTable('{{%cidade_participante}}');
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%cidade_participante}}', true) !== null) {
            return;
        }

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
}
