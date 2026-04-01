<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%participacao}}`.
 */
class m260327_180755_create_participacao_table extends Migration
{
    public function safeUp()
    {

        $this->createTable('participacao', [
            'id'              => $this->primaryKey(),
            'user_id'         => $this->integer()->notNull(),
            'trote_id'        => $this->integer()->notNull(),
            'universidade_id' => $this->integer()->notNull(),
            'curso'           => $this->string(120)->notNull(),
            'status'          => "ENUM('ativo','cancelado') NOT NULL DEFAULT 'ativo'",
            'created_at'      => $this->dateTime()->notNull(),
            'updated_at'      => $this->dateTime()->null(),
        ]);

        $this->addForeignKey('fk_part_user', 'participacao', 'user_id', 'user', 'id');
        $this->addForeignKey('fk_part_trote', 'participacao', 'trote_id', 'trote', 'id');
        $this->addForeignKey('fk_part_univ', 'participacao', 'universidade_id', 'universidade', 'id');

        $this->createIndex(
            'ux_participacao_user_trote',
            'participacao',
            ['user_id', 'trote_id'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropTable('participacao');
    }
}
