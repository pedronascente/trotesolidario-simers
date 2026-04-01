<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ranking_cache}}`.
 */
class m260327_180831_create_ranking_cache_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%ranking_cache}}', [
            'id'              => $this->primaryKey(),
            'participacao_id' => $this->integer()->notNull(),
            'trote_id'        => $this->integer()->notNull(),
            'pontuacao_total' => $this->integer()->notNull()->defaultValue(0),
            'posicao'         => $this->integer(),
            'updated_at'      => $this->dateTime()->notNull(),
        ]);

        // 🔥 1 ranking por participação
        $this->createIndex(
            'ux_ranking_participacao',
            '{{%ranking_cache}}',
            'participacao_id',
            true
        );

        // 🔎 índice para ranking
        $this->createIndex(
            'idx_ranking_trote_pontuacao',
            '{{%ranking_cache}}',
            ['trote_id', 'pontuacao_total']
        );

        $this->createIndex(
            'idx_ranking_trote_posicao',
            '{{%ranking_cache}}',
            ['trote_id', 'posicao']
        );

        // FK participação
        $this->addForeignKey(
            'fk_ranking_participacao',
            '{{%ranking_cache}}',
            'participacao_id',
            '{{%participacao}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // FK trote
        $this->addForeignKey(
            'fk_ranking_trote',
            '{{%ranking_cache}}',
            'trote_id',
            '{{%trote}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_ranking_trote', '{{%ranking_cache}}');
        $this->dropForeignKey('fk_ranking_participacao', '{{%ranking_cache}}');

        $this->dropTable('{{%ranking_cache}}');
    }
}
