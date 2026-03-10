<?php

use yii\db\Migration;

class m260309_183304_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id'                  => $this->primaryKey(),
            'trote_id'            => $this->integer(),
            'name'                => $this->string(250),
            'passwordHash'        => $this->string(255),
            'email'               => $this->string(150)->unique(),
            'status'              => $this->integer(),
            'created_at'          => $this->dateTime(),
            'updated_at'          => $this->dateTime(),
            'username'            => $this->string(255),
            'passwordResetToken'  => $this->string(255),
            'authKey'             => $this->string(255),
            'estudante'           => $this->text(),
            'estudanteMedicina'   => $this->text(),
            'estudanteOutros'     => $this->text(),
            'instituicao'         => $this->integer(),
            'outraInstituicao'    => $this->text(),
            'telefone'            => $this->text(),
            'previsaoFormatura'   => $this->text(),
            'conheceONas'         => $this->text(),
            'cpf'                 => $this->string(11)->notNull()->unique(),
            'politicaPrivacidade' => $this->text(),
            'politicaImagem'      => $this->text(),
            'administrator'       => $this->boolean(),
        ]);

        // índice
        $this->createIndex(
            'idx-user-trote_id',
            '{{%user}}',
            'trote_id'
        );

        // foreign key
        $this->addForeignKey(
            'fk-user-trote',
            '{{%user}}',
            'trote_id',
            '{{%trote}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user-trote', '{{%user}}');
        $this->dropIndex('idx-user-trote_id', '{{%user}}');
        $this->dropTable('{{%user}}');
    }
}


