<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\MercadoParceiro;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class MercadosParceirosController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [[
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function () {
                        return Yii::$app->user->identity->isAdmin();
                    },
                ]],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }

                    throw new ForbiddenHttpException('Acesso negado');
                },
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->layout = 'adminsemjquery';

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MercadoParceiro::find()->orderBy(['nome_mercado' => SORT_ASC]),
        ]);

        return $this->render('index', compact('dataProvider'));
    }

    public function actionCreate()
    {
        $model = new MercadoParceiro();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Mercado parceiro cadastrado com sucesso.');

            return $this->redirect(['index']);
        }

        return $this->render('create', compact('model'));
    }

    public function actionDelete($id)
    {
        $model = MercadoParceiro::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('Mercado parceiro não encontrado.');
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Mercado parceiro excluído com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível excluir o mercado parceiro.');
        }

        return $this->redirect(['index']);
    }



}
