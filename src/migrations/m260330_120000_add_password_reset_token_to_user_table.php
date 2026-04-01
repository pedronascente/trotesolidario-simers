<?php

use yii\db\Migration;

class m260330_120000_add_password_reset_token_to_user_table extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->schema->getTableSchema('{{%user}}', true);
        if ($schema === null) {
            return;
        }

        if (!isset($schema->columns['password_reset_token'])) {
            $this->addColumn('{{%user}}', 'password_reset_token', $this->string(255)->null()->after('authKey'));
            $schema = $this->db->schema->getTableSchema('{{%user}}', true);
        }

        if (!isset($schema->columns['password_reset_token'])) {
            return;
        }

        $indexes = $this->db->schema->findUniqueIndexes($schema);
        foreach ($indexes as $columns) {
            if ($columns === ['password_reset_token']) {
                return;
            }
        }

        $this->createIndex('idx-user-password_reset_token', '{{%user}}', 'password_reset_token', true);
    }

    public function safeDown()
    {
        $schema = $this->db->schema->getTableSchema('{{%user}}', true);
        if ($schema === null || !isset($schema->columns['password_reset_token'])) {
            return;
        }

        $indexes = $this->db->schema->findUniqueIndexes($schema);
        foreach ($indexes as $name => $columns) {
            if ($columns === ['password_reset_token']) {
                $this->dropIndex($name, '{{%user}}');
                break;
            }
        }

        $this->dropColumn('{{%user}}', 'password_reset_token');
    }
}