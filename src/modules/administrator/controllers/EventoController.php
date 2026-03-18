<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\Evento;
use app\modules\common\models\EventoSearchModel;
use app\modules\common\models\Trote;
use app\modules\common\services\contracts\EventoServiceInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class EventoController extends Controller
{
    private EventoServiceInterface $service;

    public function __construct(
        $id,
        $module,
        EventoServiceInterface $service,
        $config = []
    ) {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function beforeAction($action)
    {
        $this->layout = 'adminsemjquery';
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel  = new EventoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionView($id)
    {
        $model = $this->service->findModel($id);

        if (!$model) {
            throw new NotFoundHttpException('Evento não encontrado.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new Evento();
        $trotes = $this->service->findTrotes();

        if (
            $model->load(Yii::$app->request->post()) &&
            $this->service->create($model)
        ) {
            Yii::$app->session->setFlash('success', 'Evento criado com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('create', compact('model', 'trotes'));
    }

    public function actionUpdate($id)
    {
        $evento = $this->service->findModel($id);

        if (!$evento) {
            throw new NotFoundHttpException('Evento não encontrado.');
        }
        $trotes = $this->service->findTrotes();

        if (
            $evento->load(Yii::$app->request->post()) &&
            $this->service->update($evento)
        ) {
            Yii::$app->session->setFlash('success', 'Evento atualizado com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model'  => $evento,
            'trotes' => $trotes, 
        ]);
    }

    public function actionDelete($id)
    {
        $evento = $this->service->findModel($id);

        if (!$evento) {
            throw new NotFoundHttpException('Evento não encontrado.');
        }

        if ($this->service->delete($evento)) {
            Yii::$app->session->setFlash('success', 'Evento excluído com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao excluir Evento.');
        }

        return $this->redirect(['index']);
    }
}
