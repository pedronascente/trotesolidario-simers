<?php

use yii\db\Migration;

class m260327_180816_create_doacao_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('doacao', [
            'id'               => $this->primaryKey(),
            'participacao_id'  => $this->integer()->notNull(),
            'tipo_doacao_id'   => $this->integer()->notNull(),
            'evento_id'        => $this->integer()->null(),
            'cpf_snapshot'     => $this->string(14)->notNull(),
            'edicao_snapshot'  => $this->string(10)->notNull(),
            'arquivo'          => $this->string()->notNull(),
            'status'           => "ENUM('pendente','aprovada','rejeitada') NOT NULL DEFAULT 'pendente'",
            'motivo_reprovado' => $this->text()->null(),
            'validado_por'     => $this->integer()->null(),
            'validado_em'      => $this->dateTime()->null(),
            'created_at'       => $this->dateTime()->notNull(),
            'updated_at'       => $this->dateTime()->null(),
        ]);

        $this->addForeignKey('fk_doacao_part', 'doacao', 'participacao_id', 'participacao', 'id');
        $this->addForeignKey('fk_doacao_tipo', 'doacao', 'tipo_doacao_id', 'tipo_doacao', 'id');
        $this->addForeignKey('fk_doacao_evento', 'doacao', 'evento_id', 'evento', 'id');
        $this->addForeignKey('fk_doacao_admin', 'doacao', 'validado_por', 'user', 'id');

        // 🔥 REGRA DE NEGÓCIO PRINCIPAL
        $this->createIndex(
            'ux_doacao_cpf_edicao_tipo',
            'doacao',
            ['cpf_snapshot', 'edicao_snapshot', 'tipo_doacao_id'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropTable('doacao');
    }
}
