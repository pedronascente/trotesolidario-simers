<?php

use yii\db\Migration;

class m260309_183247_create_banner_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%banner}}', [
            'id'         => $this->primaryKey(),
            'tipo'       => $this->string(255),
            'img_dsk'    => $this->string(255),
            'img_mob'    => $this->string(255),
            'ativo'      => $this->boolean(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%banner}}');
    }
}
