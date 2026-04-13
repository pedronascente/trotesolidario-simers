<?php

use yii\db\Migration;

class m260407_150000_create_participacao_universidade_change_request_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('participacao_universidade_change_request', [
            'id' => $this->primaryKey(),
            'participacao_id' => $this->integer()->notNull(),
            'old_universidade_id' => $this->integer()->notNull(),
            'new_universidade_id' => $this->integer()->notNull(),
            'motivo' => $this->text()->notNull(),
            'status' => "ENUM('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente'",
            'requested_by' => $this->integer()->notNull(),
            'reviewed_by' => $this->integer()->null(),
            'review_notes' => $this->text()->null(),
            'created_at' => $this->dateTime()->notNull(),
            'reviewed_at' => $this->dateTime()->null(),
        ]);

        $this->addForeignKey('fk_req_participacao', 'participacao_universidade_change_request', 'participacao_id', 'participacao', 'id');
        $this->addForeignKey('fk_req_old_univ', 'participacao_universidade_change_request', 'old_universidade_id', 'universidade', 'id');
        $this->addForeignKey('fk_req_new_univ', 'participacao_universidade_change_request', 'new_universidade_id', 'universidade', 'id');
        $this->addForeignKey('fk_req_requested_by', 'participacao_universidade_change_request', 'requested_by', 'user', 'id');
        $this->addForeignKey('fk_req_reviewed_by', 'participacao_universidade_change_request', 'reviewed_by', 'user', 'id');

        $this->createIndex('idx_req_status_created', 'participacao_universidade_change_request', ['status', 'created_at']);
    }

    public function safeDown()
    {
        $this->dropTable('participacao_universidade_change_request');
    }
}
