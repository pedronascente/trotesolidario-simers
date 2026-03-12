

<?php

use yii\db\Migration;

class m260309_182854_create_trote_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%trote}}', [
            'id'            => $this->primaryKey(),
            'titulo'        => $this->string(255),
            'numero_edicao' => $this->integer(),
            'ano'           => $this->integer(),
            'descricao'     => $this->text(),
            'status'        => $this->string(20),
            'data_inicio'   => $this->date(),
            'data_fim'      => $this->date(),
            'ativo'         => $this->boolean(),
            'created_at'    => $this->dateTime(),
            'updated_at'    => $this->dateTime(),
        ]);
    }
 
    public function safeDown()
    {
        $this->dropTable('{{%trote}}');
    }
}
