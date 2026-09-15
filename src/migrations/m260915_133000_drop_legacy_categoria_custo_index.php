<?php

use yii\db\Migration;

class m260915_133000_drop_legacy_categoria_custo_index extends Migration
{
    public function safeUp()
    {
        $tableName = $this->db->schema->getRawTableName('{{%categoria_custo}}');
        $indexExists = (bool) $this->db->createCommand(
            'SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS '
            . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND INDEX_NAME = :index'
        )->bindValues([
            ':table' => $tableName,
            ':index' => 'uq-categoria_custo-trote-categoria',
        ])->queryScalar();

        if ($indexExists) {
            $this->dropIndex('uq-categoria_custo-trote-categoria', '{{%categoria_custo}}');
        }
    }

    public function safeDown()
    {
        // O indice removido e um residuo invalido da primeira execucao parcial.
        return true;
    }
}
