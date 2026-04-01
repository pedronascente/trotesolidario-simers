<?php

use yii\db\Migration;

class m260327_180810_create_evento_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%evento}}', [
            'id'          => $this->primaryKey(),
            'trote_id'    => $this->integer()->notNull(),
            'nome'        => $this->string(150)->notNull(),
            'data_evento' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex(
            'idx-evento-trote_id',
            'evento',
            'trote_id'
        );

        $this->addForeignKey(
            'fk_evento_trote',
            'evento',
            'trote_id',
            'trote',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropTable('evento');
    }
}
