<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\modules\common\models\Trote;
use app\modules\common\models\TroteSearchModel;
use app\modules\common\services\contracts\TroteServiceInterface;

class TroteController extends Controller
{
    private TroteServiceInterface $service;

    public function __construct(
        $id,
        $module,
        TroteServiceInterface $service,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors(){
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(){
        $this->layout = 'adminsemjquery';

        $searchModel  = new TroteSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id){
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate(){
        $this->layout = 'adminsemjquery';
        $trote = new Trote();
        $trote->ativo = 1;
        if ($trote->load(Yii::$app->request->post())) {

            if ($this->service->create($trote)) {
                Yii::$app->session->setFlash('success', 'Trote criado com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao criar Trote');

            //Yii::error($trote->getErrors(), __METHOD__);
            //Yii::$app->session->setFlash('error', print_r($trote->getErrors(), true));
        }

        return $this->render('create', [
            'model' => $trote,
        ]);
    }

    public function actionUpdate($id){
        $this->layout = 'adminsemjquery';
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if ($this->service->update($model)) {
                Yii::$app->session->setFlash('success', 'Trote atualizado com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao atualizar Trote');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id){
        $model = $this->findModel($id);

        if ($this->service->delete($model)) {
            Yii::$app->session->setFlash('success', 'Status alterado com sucesso');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao alterar status');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id): Trote{
        if (($model = Trote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Trote não encontrado.');
    }
}
