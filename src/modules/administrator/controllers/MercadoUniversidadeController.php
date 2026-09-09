<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\MercadoUniversidade;
use app\modules\common\models\MercadoParceiro;
use app\modules\common\models\Universidade;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class MercadoUniversidadeController extends Controller
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
            'query' => MercadoUniversidade::find()
                ->with(['mercado', 'universidade'])
                ->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new MercadoUniversidade();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Mercado vinculado à universidade com sucesso.');

            return $this->redirect(['index']);
        }

        $mercados = ArrayHelper::map(
            MercadoParceiro::find()->orderBy(['nome_mercado' => SORT_ASC])->all(),
            'id',
            static function (MercadoParceiro $mercado) {
                return $mercado->nome_mercado . ' — ' . $mercado->endereco;
            }
        );
        $universidades = ArrayHelper::map(
            Universidade::find()->orderBy(['nome' => SORT_ASC])->all(),
            'id',
            'nome'
        );

        return $this->render('create', compact('model', 'mercados', 'universidades'));
    }

    
}
