<?php

use yii\db\Migration;
use yii\db\Query;

class m260924_132720_ass_trote_id_to_mercado_universidade_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            '{{%mercado_universidade}}',
            'trote_id',
            $this->integer()->null()->after('universidade_id')
        );

        $possuiVinculosExistentes = (new Query())
            ->from('{{%mercado_universidade}}')
            ->exists($this->db);

        if ($possuiVinculosExistentes) {
            $troteAtivoId = (new Query())
                ->select('id')
                ->from('{{%trote}}')
                ->where(['status' => 'ativo'])
                ->orderBy(['id' => SORT_DESC])
                ->scalar($this->db);

            if ($troteAtivoId === false) {
                throw new \RuntimeException('Não existe trote ativo para vincular os mercados já cadastrados.');
            }

            $this->update(
                '{{%mercado_universidade}}',
                ['trote_id' => (int) $troteAtivoId],
                ['trote_id' => null]
            );
        }

        $this->alterColumn(
            '{{%mercado_universidade}}',
            'trote_id',
            $this->integer()->notNull()
        );

        $this->createIndex(
            'idx-mercado_universidade-trote_id',
            '{{%mercado_universidade}}',
            'trote_id'
        );

        $this->addForeignKey(
            'fk-mercado_universidade-trote_id',
            '{{%mercado_universidade}}',
            'trote_id',
            '{{%trote}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // A combinação passa a ser única dentro de cada trote.
        $this->createIndex(
            'uq-mercado_universidade-mercado-universidade-trote',
            '{{%mercado_universidade}}',
            ['mercado_id', 'universidade_id', 'trote_id'],
            true
        );

        $this->dropIndex(
            'idx-mercado_universidade-unico',
            '{{%mercado_universidade}}'
        );
    }

    public function safeDown()
    {
        $possuiDuplicidadeEntreTrotes = (new Query())
            ->select('mercado_id')
            ->from('{{%mercado_universidade}}')
            ->groupBy(['mercado_id', 'universidade_id'])
            ->having('COUNT(*) > 1')
            ->exists($this->db);

        if ($possuiDuplicidadeEntreTrotes) {
            throw new \RuntimeException(
                'A migration não pode ser revertida porque há vínculos repetidos de mercado e universidade em trotes diferentes.'
            );
        }

        $this->createIndex(
            'idx-mercado_universidade-unico',
            '{{%mercado_universidade}}',
            ['mercado_id', 'universidade_id'],
            true
        );

        $this->dropIndex(
            'uq-mercado_universidade-mercado-universidade-trote',
            '{{%mercado_universidade}}'
        );

        $this->dropForeignKey(
            'fk-mercado_universidade-trote_id',
            '{{%mercado_universidade}}'
        );

        $this->dropIndex(
            'idx-mercado_universidade-trote_id',
            '{{%mercado_universidade}}'
        );

        $this->dropColumn(
            '{{%mercado_universidade}}',
            'trote_id'
        );
    }
}
