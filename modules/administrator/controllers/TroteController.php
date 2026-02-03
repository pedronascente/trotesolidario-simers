<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\Helper;
use Yii;
use app\modules\common\models\Trote;
use app\modules\common\models\TroteSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class TroteController extends Controller{
    
    public function behaviors(){
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
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
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    public function actionView($id){
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate(){
        $model = new Trote();
        $model->ativo = 1;
        $this->layout = 'adminsemjquery';

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->save(false)) {
                Yii::$app->session->setFlash('success', 'Trote criada com sucesso');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao criar Trote');
            }
        }
        return $this->render('create', ['model' => $model]); 
    }

    public function actionUpdate($id){
        $model = $this->findModel($id);
        $this->layout = 'adminsemjquery';

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->save(false)) {
                Yii::$app->session->setFlash('success', 'Trote atualizada com sucesso');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao atualizar Trote');
            }
        }
        return $this->render('update', ['model' => $model]);
    }

  
    public function actionDelete($id){
        $model = $this->findModel($id);
        $model->ativo = ($model->ativo == 1) ? 0 : 1;

        if (!$model->save()) {
            Helper::d($model->getErrors());
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
{
        if (($model = Trote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
