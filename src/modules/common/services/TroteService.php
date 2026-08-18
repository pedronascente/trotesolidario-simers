<?php

namespace app\modules\common\services;

use Yii;
use DomainException;
use RuntimeException;
use app\modules\common\models\Participacao;
use app\modules\common\models\Trote;
use app\modules\common\services\contracts\TroteServiceInterface;

class TroteService implements TroteServiceInterface
{
    public function create(Trote $trote): bool
    {
        return Yii::$app->db->transaction(function () use ($trote)
        {
            $this->aplicarDataEncerramento($trote);

            if (!$trote->validate())
            {
                return false;
            }
            // Se estiver criando como ATIVO
            if ($trote->status == Trote::STATUS_ATIVO) {
                $this->encerrarOutros($trote->id ?? null);
            }

            if (!$trote->save(false)) {
                return false;
            }

            $this->encerrarParticipacoes($trote);
            return true;
        });
    }

    public function update(Trote $trote): bool
    {
        return Yii::$app->db->transaction(function () use ($trote)
        {
            $this->aplicarDataEncerramento($trote);

            if (!$trote->validate())
            {
                return false;
            }

            // Se alterou para ATIVO
            if ($trote->status == Trote::STATUS_ATIVO) 
            {
                $this->encerrarOutros($trote->id);
            }

            if (!$trote->save(false)) {
                return false;
            }

            $this->encerrarParticipacoes($trote);
            return true;
        });
    }

    /**
     * Define trote como ATIVO e encerra os demais
     */
    public function ativar(Trote $trote): bool
    {
        return Yii::$app->db->transaction(function () use ($trote) 
        {
            // Encerra todos os outros
            $this->encerrarOutros($trote->id);

            // Ativa o atual
            $trote->status = Trote::STATUS_ATIVO;

            return $trote->save(false);
        });
    }

    /**
     * Encerra todos os trotes exceto o informado
     */
    private function encerrarOutros(?int $idAtual = null): void
    {
        Trote::updateAll(
            [
                'status' => Trote::STATUS_ENCERRADO,
                'data_fim' => date('Y-m-d'),
            ],
            $idAtual ? ['<>', 'id', $idAtual] : []
        );

        Participacao::updateAll(
            ['status' => Participacao::STATUS_ENCERRADO],
            $idAtual ? ['<>', 'trote_id', $idAtual] : []
        );
    }

    private function aplicarDataEncerramento(Trote $trote): void
    {
        if ($trote->status === Trote::STATUS_ENCERRADO) {
            $trote->data_fim = date('Y-m-d');
        }
    }

    private function encerrarParticipacoes(Trote $trote): void
    {
        if ($trote->status === Trote::STATUS_ENCERRADO && $trote->id !== null) {
            Participacao::updateAll(
                ['status' => Participacao::STATUS_ENCERRADO],
                ['trote_id' => $trote->id]
            );
        }
    }

    public function delete(Trote $trote): void
    {
        Yii::$app->db->transaction(function () use ($trote) 
        {
            if (!$trote) 
            {
                throw new DomainException("Trote não encontrado.");
            }

            // RNF001 – Restrição de Exclusão
            if ($trote->possuiVinculos()) 
            {
                throw new DomainException(
                    "Não é possível excluir este trote, pois ele está vinculado a outros registros no sistema."
                );
            }

            if ($trote->delete() === false) 
            {
                throw new RuntimeException('Erro ao excluir o trote.');
            }
        });
    }
}
