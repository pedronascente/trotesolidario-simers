<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%certificado}}`.
 */
class m260327_180823_create_certificado_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('certificado', [
            'id'                  => $this->primaryKey(),
            'participacao_id'     => $this->integer()->notNull(),
            'codigo_validador'    => $this->string(50)->notNull()->unique(),
            'carga_horaria_total' => $this->integer()->notNull()->defaultValue(0),
            'arquivo_pdf'         => $this->string()->null(),
            'data_emissao'        => $this->dateTime()->notNull(),
            'hash_integridade'    => $this->string()->notNull(),
            'emitido_por'         => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_cert_part', 'certificado', 'participacao_id', 'participacao', 'id');
        $this->addForeignKey('fk_cert_admin', 'certificado', 'emitido_por', 'user', 'id');

        $this->createIndex(
            'ux_cert_participacao',
            'certificado',
            ['participacao_id'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropTable('certificado');
    }
}
