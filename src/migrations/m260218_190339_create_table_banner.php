<?php

use yii\db\Migration;

class m260218_190339_create_table_banner extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%banner}}', [

            'id'         => $this->primaryKey(),
            'tipo'       => $this->string()->notNull(),
            'img_dsk'    => $this->string(255),
            'img_mob'    => $this->string(255),
            'ativo' => $this->boolean()->notNull()->defaultValue(1)->comment('1 = ativo, 0 = inativo'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%banner}}');
    }
}
