<?php

use yii\db\Migration;

class m260309_185554_create_certificado_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%certificado}}', [
            'id'                    => $this->primaryKey(),
            'user_id'               => $this->integer()->notNull(),
            'trote_id'              => $this->integer()->notNull(),
            'codigo_validacao'      => $this->string(200)->notNull()->unique(),
            'carga_horaria_total'   => $this->integer()->notNull(),
            'arquivo'               => $this->string(255),
            'data_emissao'          => $this->dateTime(),
            'hash_integridade'      => $this->string(20),
            'emitido_por'           => $this->integer()->notNull(),
            'created_at'            => $this->dateTime(),
            'updated_at'            => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%certificado}}');
    }
}