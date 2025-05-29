<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification}}`.
 */
class m240321_000001_create_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'type' => $this->string(50)->notNull(),
            'read' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Adiciona índice para user_id
        $this->createIndex(
            'idx-notification-user_id',
            '{{%notification}}',
            'user_id'
        );

        // Adiciona chave estrangeira para users
        $this->addForeignKey(
            'fk-notification-user_id',
            '{{%notification}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-notification-user_id', '{{%notification}}');
        $this->dropIndex('idx-notification-user_id', '{{%notification}}');
        $this->dropTable('{{%notification}}');
    }
} 