<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\MercadoUniversidade;
use app\modules\common\models\MercadoParceiro;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
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
            'query' => MercadoUniversidade::find()
                ->with(['trote', 'mercado', 'universidade'])
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
        $trotes = ArrayHelper::map(
            Trote::getAtivos(),
            'id',
            static function (Trote $trote) {
                return $trote->titulo . ' — ' . $trote->edicao;
            }
        );
        $universidades = ArrayHelper::map(
            Universidade::find()->orderBy(['nome' => SORT_ASC])->all(),
            'id',
            'nome'
        );

        return $this->render('create', compact('model', 'trotes', 'mercados', 'universidades'));
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete() !== false) {
            Yii::$app->session->setFlash('success', 'Vínculo removido com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível remover o vínculo.');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id): MercadoUniversidade
    {
        if (($model = MercadoUniversidade::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Vínculo entre mercado e universidade não encontrado.');
    }
}
