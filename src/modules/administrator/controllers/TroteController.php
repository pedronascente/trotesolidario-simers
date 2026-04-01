<?php

namespace app\modules\administrator\controllers;

use DomainException;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\modules\common\models\Trote;
use app\modules\common\models\TroteSearchModel;
use app\modules\common\services\contracts\TroteServiceInterface;

class TroteController extends Controller
{
    private TroteServiceInterface $service;

    public function __construct($id, $module, TroteServiceInterface $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
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
        $searchModel = new TroteSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $trote = new Trote();

        if ($trote->load(Yii::$app->request->post())) {
            if ($this->service->create($trote)) {
                Yii::$app->session->setFlash('success', 'Trote criado com sucesso');

                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao criar trote');
        }

        return $this->render('create', [
            'model' => $trote,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($this->service->update($model)) {
                Yii::$app->session->setFlash('success', 'Trote atualizado com sucesso');

                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao atualizar trote');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        try {
            $this->service->delete($this->findModel($id));
            Yii::$app->session->setFlash('success', 'Trote excluido com sucesso');
        } catch (DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', 'Erro ao excluir trote');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id): Trote
    {
        if (($model = Trote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Trote nao encontrado.');
    }
}
