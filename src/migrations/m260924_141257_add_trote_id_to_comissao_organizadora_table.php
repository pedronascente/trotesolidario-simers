<?php

use yii\db\Migration;
use yii\db\Query;

class m260924_141257_add_trote_id_to_comissao_organizadora_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%comissao_organizadora}}', 'trote_id', $this->integer()->null()->after('universidade_id'));

        $possuiMembrosExistentes = (new Query())->from('{{%comissao_organizadora}}')->exists($this->db);
        if ($possuiMembrosExistentes) {
            $troteAtivoId = (new Query())
                ->select('id')
                ->from('{{%trote}}')
                ->where(['status' => 'ativo'])
                ->orderBy(['id' => SORT_DESC])
                ->scalar($this->db);

            if ($troteAtivoId === false) {
                throw new \RuntimeException('Não existe trote ativo para vincular a comissão já cadastrada.');
            }

            $this->update('{{%comissao_organizadora}}', ['trote_id' => (int) $troteAtivoId], ['trote_id' => null]);
        }

        $this->alterColumn('{{%comissao_organizadora}}', 'trote_id', $this->integer()->notNull());
        $this->createIndex('idx-comissao_organizadora-trote_id', '{{%comissao_organizadora}}', 'trote_id');
        $this->addForeignKey(
            'fk-comissao_organizadora-trote_id',
            '{{%comissao_organizadora}}',
            'trote_id',
            '{{%trote}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->dropIndex('idx-comissao_organizadora-publicacao', '{{%comissao_organizadora}}');
        $this->createIndex(
            'idx-comissao_organizadora-publicacao',
            '{{%comissao_organizadora}}',
            ['trote_id', 'universidade_id', 'ativo', 'ordem']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-comissao_organizadora-publicacao', '{{%comissao_organizadora}}');
        $this->createIndex(
            'idx-comissao_organizadora-publicacao',
            '{{%comissao_organizadora}}',
            ['universidade_id', 'ativo', 'ordem']
        );
        $this->dropForeignKey('fk-comissao_organizadora-trote_id', '{{%comissao_organizadora}}');
        $this->dropIndex('idx-comissao_organizadora-trote_id', '{{%comissao_organizadora}}');
        $this->dropColumn('{{%comissao_organizadora}}', 'trote_id');
    }
}
