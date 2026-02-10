<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use app\modules\common\models\Informativo;
use app\modules\common\models\InformativoSearchModel;
use app\modules\common\services\contracts\InformativoServiceInterface;

class InformativoController extends Controller{

    private InformativoServiceInterface $service;

    public function __construct(
        $id,
        $module,
        InformativoServiceInterface $service,
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
        $searchModel = new InformativoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->layout = 'adminsemjquery';
        return $this->render('index', [
            'searchModel' => $searchModel,
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
        $informativo = new Informativo();
        
        if($informativo->load(Yii::$app->request->post())){
            if($this->service->create($informativo)){
                Yii::$app->session->setFlash('success','Informativo criado com sucesso');
                return $this->redirect(['index']);
            }
            Yii::$app->session->setFlash('error', 'Erro ao criar Informativo');
        }

        return $this->render('create', [
            'model'=>$informativo
        ]);
    }

    public function actionUpdate($id){
         
        $this->layout = 'adminsemjquery';
        $informativo = $this->findModel($id);
       
        if($informativo->load(Yii::$app->request->post())){
            if($this->service->update($informativo)){
                Yii::$app->session->setFlash('success', 'Informativo atualizada com sucesso');
                return $this->redirect(['index']);
            }  
            Yii::$app->session->setFlash('error', 'Erro ao atualizar Informativo');          
        }  
        return $this->render('update', [
            'model' => $informativo,
        ]);
    }

    public function actionDelete($id){
        $this->layout = 'adminsemjquery';
        $informativo = $this->findModel($id);
      
        if($this->service->delete($informativo)){
             Yii::$app->session->setFlash('success', 'Informativo excluído com sucesso');
        } else {
             Yii::$app->session->setFlash('error', 'Erro ao excluir Informativo');
        }
        return $this->redirect(['index']);    
    }

    protected function findModel($id){
        if (($model = Informativo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
