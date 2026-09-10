<?php

use yii\db\Migration;

class m260909_210000_create_album_foto_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%album_foto}}', [
            'id' => $this->primaryKey(),
            'participacao_id' => $this->integer()->notNull(),
            'titulo' => $this->string(160)->notNull(),
            'imagem' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('idx-album_foto-participacao_id', '{{%album_foto}}', 'participacao_id');
        $this->addForeignKey(
            'fk-album_foto-participacao_id',
            '{{%album_foto}}',
            'participacao_id',
            '{{%participacao}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-album_foto-participacao_id', '{{%album_foto}}');
        $this->dropTable('{{%album_foto}}');
    }
}
