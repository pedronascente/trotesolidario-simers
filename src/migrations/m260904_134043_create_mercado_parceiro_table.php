<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mercado_parceiro}}`.
 */
class m260904_134043_create_mercado_parceiro_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mercado_parceiro}}', [
            'id' => $this->primaryKey(),
            'nome_mercado' => $this->string(255)->notNull(),
            'endereco' => $this->string(255)->notNull(),
            'numero' => $this->string(20)->notNull(),
            'bairro' => $this->string(100)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mercado_parceiro}}');
    }
}
