<?php

use yii\db\Migration;

class m260909_220000_create_comissao_organizadora_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%comissao_organizadora}}', [
            'id' => $this->primaryKey(),
            'universidade_id' => $this->integer()->notNull(),
            'nome' => $this->string(180)->notNull(),
            'cargo' => $this->string(120)->null(),
            'ordem' => $this->integer()->notNull()->defaultValue(0),
            'ativo' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('idx-comissao_organizadora-universidade_id', '{{%comissao_organizadora}}', 'universidade_id');
        $this->createIndex('idx-comissao_organizadora-publicacao', '{{%comissao_organizadora}}', ['universidade_id', 'ativo', 'ordem']);
        $this->addForeignKey(
            'fk-comissao_organizadora-universidade_id',
            '{{%comissao_organizadora}}',
            'universidade_id',
            '{{%universidade}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-comissao_organizadora-universidade_id', '{{%comissao_organizadora}}');
        $this->dropTable('{{%comissao_organizadora}}');
    }
}
