<?php

use yii\db\Migration;

class m260327_180506_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id'                   => $this->primaryKey(),
            'nome'                 => $this->string(255)->notNull(),
            'email'                => $this->string(255)->notNull(),
            'username'             => $this->string(80)->notNull()->unique(),
            'cpf'                  => $this->string(14)->notNull(),
            'password_hash'        => $this->string(255)->notNull(),
            'role'                 => "ENUM('admin','participante') NOT NULL",
            'authKey'              => $this->string(255),
            'password_reset_token' => $this->string(255),
            'status'               => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at'           => $this->dateTime()->notNull(),
            'updated_at'           => $this->dateTime()->notNull(),
        ]);

        // indices unicos
        $this->createIndex('idx-user-cpf', '{{%user}}', 'cpf', true);
        $this->createIndex('idx-user-email', '{{%user}}', 'email', true);
        $this->createIndex('idx-user-password_reset_token', '{{%user}}', 'password_reset_token', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}