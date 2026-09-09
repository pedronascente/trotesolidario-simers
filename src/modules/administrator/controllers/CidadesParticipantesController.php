<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\CidadeParticipante;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class CidadesParticipantesController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
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
            'query' => CidadeParticipante::find()->orderBy(['cidade' => SORT_ASC, 'uf' => SORT_ASC]),
        ]);

        return $this->render('index', compact('dataProvider'));
    }

    public function actionCreate()
    {
        $model = new CidadeParticipante();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Cidade participante cadastrada com sucesso.');

            return $this->redirect(['index']);
        }

        return $this->render('create', compact('model'));
    }

    public function actionDelete($id)
    {
        $model = CidadeParticipante::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('Cidade participante não encontrada.');
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Cidade participante excluída com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível excluir a cidade participante.');
        }

        return $this->redirect(['index']);
    }
}
