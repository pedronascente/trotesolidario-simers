<?php

use yii\db\Migration; 

class m260212_144341_documentos extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%documentos}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(255)->notNull(),
            'arquivo' => $this->string(255),
            'tipo' => $this->string(20)->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Índice para filtro por tipo
        $this->createIndex(
            'idx-documentos-tipo',
            '{{%documentos}}',
            'tipo'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%documentos}}');
    }
}
