<?php

namespace app\modules\common\services;

use app\modules\common\models\Certificado;
use yii\web\NotFoundHttpException;

/**
 * Responsável por queries e acesso a dados de certificados
 */
class CertificadoRepository
{
    public function findByIdAndUserId(int $id, int $userId): Certificado
    {
        $model = Certificado::find()
            ->with(['participacao.user', 'participacao.trote', 'participacao.universidade', 'emissor'])
            ->joinWith('participacao')
            ->where([
                'certificado.id' => $id,
                'participacao.user_id' => $userId,
            ])
            ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Certificado nao encontrado.');
    }

    /**
     * Encontra todos os certificados do usuário com relações carregadas
     */
    public function findAllByUserId(int $userId): array
    {
        return Certificado::find()
            ->alias('c')
            ->joinWith([
                'participacao p' => function ($q) {
                    $q->joinWith([
                        'trote',
                        'universidade',
                        'doacoes.tipoDoacao'
                    ]);
                }
            ])
            ->where(['p.user_id' => $userId])
            ->orderBy([
                'c.data_emissao' => SORT_DESC,
                'c.id' => SORT_DESC
            ])
            ->all();
    }
}
