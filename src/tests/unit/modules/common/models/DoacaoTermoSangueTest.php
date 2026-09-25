<?php

namespace tests\unit\modules\common\models;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/TipoDoacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Doacao.php';

use app\modules\common\models\Doacao;
use app\modules\common\models\TipoDoacao;
use Codeception\Test\Unit;

class DoacaoTermoSangueTest extends Unit
{
    public function testTermoObrigatorioParaDoacaoDeSangueDoParticipante(): void
    {
        $model = new DoacaoTermoSangueFake();
        $model->scenario = Doacao::SCENARIO_PARTICIPANTE_CREATE;
        $model->tipo_doacao_id = 4;
        $model->tipoSangue = true;

        $this->assertFalse($model->validate(['termo_doacao_sangue']));
        $this->assertSame(
            ['Você precisa aceitar este termo.'],
            $model->getErrors('termo_doacao_sangue')
        );

        $model->termo_doacao_sangue = 1;

        $this->assertTrue($model->validate(['termo_doacao_sangue']));
    }

    public function testTermoNaoObrigatorioParaOutroTipoDeDoacao(): void
    {
        $model = new DoacaoTermoSangueFake();
        $model->scenario = Doacao::SCENARIO_PARTICIPANTE_CREATE;
        $model->tipo_doacao_id = 1;

        $this->assertTrue($model->validate(['termo_doacao_sangue']));
    }

    public function testTermoNaoAfetaCenariosExistentes(): void
    {
        $model = new DoacaoTermoSangueFake();
        $model->tipo_doacao_id = 4;
        $model->tipoSangue = true;

        $this->assertTrue($model->validate(['termo_doacao_sangue']));
    }

    public function testIdentificaNomeDoTipoSangueSemDependerDoId(): void
    {
        $this->assertTrue(TipoDoacao::isNomeSangue('Doação de Sangue'));
        $this->assertTrue(TipoDoacao::isNomeSangue('Sangue'));
        $this->assertFalse(TipoDoacao::isNomeSangue('Doação de Roupas'));
    }
}

class DoacaoTermoSangueFake extends Doacao
{
    public $tipoSangue = false;

    public function isTipoDoacaoSangue(): bool
    {
        return $this->tipoSangue;
    }

    public function attributes(): array
    {
        return [
            'id',
            'participacao_id',
            'tipo_doacao_id',
            'evento_id',
            'arquivo',
            'status',
            'motivo_reprovado',
            'validado_por',
            'validado_em',
            'cpf_snapshot',
            'edicao_snapshot',
            'created_at',
            'updated_at',
        ];
    }
}
