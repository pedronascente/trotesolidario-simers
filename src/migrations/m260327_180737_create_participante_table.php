<?php

use yii\db\Migration;


class m260327_180737_create_participante_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('participante', [
            'id'                 => $this->primaryKey(),
            'user_id'            => $this->integer()->notNull()->unique(),
            'estudante'          => $this->boolean()->notNull()->defaultValue(true),
            'estudante_medicina' => $this->boolean()->notNull()->defaultValue(false),
            'previsao_formatura' => $this->dateTime()->null(),
        ]);

        $this->addForeignKey(
            'fk_participante_user',
            'participante',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-participante-user', '{{%participante}}');
        $this->dropTable('{{%participante}}');
    }
}
