<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\Participacao;
use app\modules\common\models\ParticipacaoSearchModel;
use app\modules\common\services\contracts\ParticipacaoServiceInterface;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ParticipacaoController extends Controller
{
    private ParticipacaoServiceInterface $service;

    public function __construct($id, $module, ParticipacaoServiceInterface $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
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
        $searchModel = new ParticipacaoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionCreate()
    {
        $model = new Participacao();
        $users = $this->service->findUsers();
        $trotes = $this->service->findTrotes();
        $universidades = $this->service->findUniversidades();

        if ($model->load(Yii::$app->request->post()) && $this->service->create($model)) {
            Yii::$app->session->setFlash('success', 'Participacao criada com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('create', compact('model', 'users', 'trotes', 'universidades'));
    }

    public function actionUpdate($id)
    {
        $model = $this->service->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException('Participacao nao encontrada.');
        }

        $users = $this->service->findUsers();
        $trotes = $this->service->findTrotes();
        $universidades = $this->service->findUniversidades();

        if ($model->load(Yii::$app->request->post()) && $this->service->update($model)) {
            Yii::$app->session->setFlash('success', 'Participacao atualizada com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('update', compact('model', 'users', 'trotes', 'universidades'));
    }

    public function actionDelete($id)
    {
        $model = $this->service->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException('Participacao nao encontrada.');
        }

        try {
            $this->service->delete($model);
            Yii::$app->session->setFlash('success', 'Participacao excluida com sucesso.');
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index']);
    }
}
