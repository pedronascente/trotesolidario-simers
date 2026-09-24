<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\ComissaoOrganizadora;
use app\modules\common\models\MercadoParceiro;
use app\modules\common\models\Trote;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use app\modules\common\models\Universidade;

class InstituicaoController extends Controller
{
    public function actionIndex()
    {
        $universidades = Universidade::find()
            ->where(['ativo' => 1])
            ->orderBy(['cidade' => SORT_ASC, 'nome' => SORT_ASC])
            ->all();
        return $this->render('index', [
            'universidades' => $universidades,
        ]);
    }

    public function actionDetalhes($id)
    {
        $universidade = Universidade::findOne([
            'id' => (int) $id,
            'ativo' => 1,
        ]);

        if ($universidade === null) {
            throw new NotFoundHttpException('Instituição não encontrada.');
        }

        $troteAtivo = Trote::find()
            ->where(['status' => Trote::STATUS_ATIVO])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $mercadosParceiros = [];
        $comissaoOrganizadora = [];

        if ($troteAtivo !== null) {
            $mercadosParceiros = MercadoParceiro::find()
                ->alias('mp')
                ->innerJoin('{{%mercado_universidade}} mu', '[[mu.mercado_id]] = [[mp.id]]')
                ->where([
                    'mu.universidade_id' => $universidade->id,
                    'mu.trote_id' => $troteAtivo->id,
                ])
                ->orderBy(['mp.nome_mercado' => SORT_ASC])
                ->all();

            $comissaoOrganizadora = ComissaoOrganizadora::find()
                ->alias('co')
                ->where([
                    'co.ativo' => 1,
                    'co.trote_id' => $troteAtivo->id,
                    'co.universidade_id' => $universidade->id,
                ])
                ->orderBy(['co.ordem' => SORT_ASC, 'co.nome' => SORT_ASC, 'co.id' => SORT_ASC])
                ->all();
        }

        return $this->render('detalhes', [
            'universidade' => $universidade,
            'mercadosParceiros' => $mercadosParceiros,
            'comissaoOrganizadora' => $comissaoOrganizadora,
        ]);
    }
}
