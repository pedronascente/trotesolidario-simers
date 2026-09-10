<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\ComissaoOrganizadora;
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

        $mercadosParceiros = $universidade->getMercadosParceiros()
            ->orderBy(['nome_mercado' => SORT_ASC])
            ->all();

        $comissaoOrganizadora = ComissaoOrganizadora::find()
            ->alias('co')
            ->where(['co.ativo' => 1, 'co.universidade_id' => $universidade->id])
            ->orderBy(['co.ordem' => SORT_ASC, 'co.nome' => SORT_ASC, 'co.id' => SORT_ASC])
            ->all();

        return $this->render('detalhes', [
            'universidade' => $universidade,
            'mercadosParceiros' => $mercadosParceiros,
            'comissaoOrganizadora' => $comissaoOrganizadora,
        ]);
    }
}
