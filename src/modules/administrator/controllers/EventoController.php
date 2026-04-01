<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\Evento;
use app\modules\common\models\EventoSearchModel;
use app\modules\common\services\contracts\EventoServiceInterface;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class EventoController extends Controller
{
    private EventoServiceInterface $service;

    public function __construct($id, $module, EventoServiceInterface $service, $config = [])
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
        $searchModel = new EventoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionCreate()
    {
        $model = new Evento();
        $trotes = $this->service->findTrotes();

        if ($model->load(Yii::$app->request->post()) && $this->service->create($model)) {
            Yii::$app->session->setFlash('success', 'Evento criado com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('create', compact('model', 'trotes'));
    }

    public function actionUpdate($id)
    {
        $evento = $this->service->findModel($id);
        if (!$evento) {
            throw new NotFoundHttpException('Evento nao encontrado.');
        }

        $trotes = $this->service->findTrotes();

        if ($evento->load(Yii::$app->request->post()) && $this->service->update($evento)) {
            Yii::$app->session->setFlash('success', 'Evento atualizado com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $evento,
            'trotes' => $trotes,
        ]);
    }

    public function actionDelete($id)
    {
        $evento = $this->service->findModel($id);
        if (!$evento) {
            throw new NotFoundHttpException('Evento nao encontrado.');
        }

        try {
            $this->service->delete($evento);
            Yii::$app->session->setFlash('success', 'Evento excluido com sucesso.');
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', 'Erro ao excluir evento.');
        }

        return $this->redirect(['index']);
    }
}
