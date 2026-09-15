<?php

use yii\db\Migration;

class m260915_130000_normalize_categorias_custo extends Migration
{
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%tipo_categoria_custo}}', true) === null) {
            $this->createTable('{{%tipo_categoria_custo}}', [
                'id' => $this->primaryKey(),
                'nome' => $this->string(120)->notNull()->unique(),
                'descricao' => $this->string(500),
                'ativo' => $this->boolean()->notNull()->defaultValue(1),
                'created_at' => $this->dateTime(),
                'updated_at' => $this->dateTime(),
            ]);

            $this->execute('INSERT INTO {{%tipo_categoria_custo}} (nome, ativo, created_at, updated_at) SELECT DISTINCT categoria, 1, NOW(), NOW() FROM {{%categoria_custo}} ORDER BY categoria');
        }

        $categoriaSchema = $this->db->schema->getTableSchema('{{%categoria_custo}}', true);
        if (!isset($categoriaSchema->columns['tipo_categoria_custo_id'])) {
            $this->addColumn('{{%categoria_custo}}', 'tipo_categoria_custo_id', $this->integer()->null()->after('trote_id'));
            $this->execute('UPDATE {{%categoria_custo}} c INNER JOIN {{%tipo_categoria_custo}} t ON t.nome = c.categoria SET c.tipo_categoria_custo_id = t.id');
            $this->alterColumn('{{%categoria_custo}}', 'tipo_categoria_custo_id', $this->integer()->notNull());
        }

        $indexes = $this->db->schema->getTableIndexes('{{%categoria_custo}}', true);
        if (!isset($indexes['idx-categoria_custo-trote'])) {
            $this->createIndex('idx-categoria_custo-trote', '{{%categoria_custo}}', 'trote_id');
        }
        $categoriaSchema = $this->db->schema->getTableSchema('{{%categoria_custo}}', true);
        if (isset($categoriaSchema->columns['categoria'])) {
            $this->dropIndex('uq-categoria_custo-trote-categoria', '{{%categoria_custo}}');
            $this->dropColumn('{{%categoria_custo}}', 'categoria');
        }

        $indexes = $this->db->schema->getTableIndexes('{{%categoria_custo}}', true);
        if (!isset($indexes['uq-categoria_custo-trote-tipo'])) {
            $this->createIndex('uq-categoria_custo-trote-tipo', '{{%categoria_custo}}', ['trote_id', 'tipo_categoria_custo_id'], true);
        }
        $foreignKeys = $this->db->schema->getTableForeignKeys('{{%categoria_custo}}', true);
        if (!isset($foreignKeys['fk-categoria_custo-tipo'])) {
            $this->addForeignKey('fk-categoria_custo-tipo', '{{%categoria_custo}}', 'tipo_categoria_custo_id', '{{%tipo_categoria_custo}}', 'id', 'RESTRICT', 'CASCADE');
        }
    }

    public function safeDown()
    {
        $this->addColumn('{{%categoria_custo}}', 'categoria', $this->string(120)->null()->after('trote_id'));
        $this->execute('UPDATE {{%categoria_custo}} c INNER JOIN {{%tipo_categoria_custo}} t ON t.id = c.tipo_categoria_custo_id SET c.categoria = t.nome');
        $this->alterColumn('{{%categoria_custo}}', 'categoria', $this->string(120)->notNull());
        $this->dropForeignKey('fk-categoria_custo-tipo', '{{%categoria_custo}}');
        $this->dropIndex('uq-categoria_custo-trote-tipo', '{{%categoria_custo}}');
        $this->dropColumn('{{%categoria_custo}}', 'tipo_categoria_custo_id');
        $this->createIndex('uq-categoria_custo-trote-categoria', '{{%categoria_custo}}', ['trote_id', 'categoria'], true);
        $this->dropTable('{{%tipo_categoria_custo}}');
    }
}