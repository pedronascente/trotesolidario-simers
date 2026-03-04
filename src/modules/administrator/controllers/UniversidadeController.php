<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


use app\modules\common\models\Universidade;
use app\modules\common\models\UniversidadeSearchModel;
use app\modules\common\services\contracts\UniversidadeServiceInterface;

class UniversidadeController extends Controller{

    private UniversidadeServiceInterface $service;

    public function __construct(
        $id,
        $module,
        UniversidadeServiceInterface $service,
        $config = []
    ) {
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
                        'roles' => ['@'], // somente usuários logados
                    ],
                ],
            ],
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
        $searchModel = new UniversidadeSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        $universidade = new Universidade();
      
        if ($universidade->load(Yii::$app->request->post())) {

            if ($this->service->create($universidade)) {
                Yii::$app->session->setFlash('success', 'Universidade criada com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao criar Universidade');
        }

        return $this->render('create', [
            'model' => $universidade,
        ]);
    }

    public function actionUpdate($id){

        $this->layout = 'adminsemjquery';
        $universidade = $this->findModel($id);

        if ($universidade->load(Yii::$app->request->post())) {

            if ($this->service->update($universidade)) {
                Yii::$app->session->setFlash('success', 'Universidade atualizada com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao atualizar Universidade');
        }

        return $this->render('update', [
            'model' => $universidade,
        ]);
    }

    public function actionDelete($id){

        $universidade = $this->findModel($id);
      
        if ($this->service->toggleAtivo($universidade)) {
            Yii::$app->session->setFlash('success', 'Status alterado com sucesso');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao alterar status');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id){
        
        if (($universidade = Universidade::findOne($id)) !== null) {
            return $universidade;
        }

        throw new NotFoundHttpException('Universidade não encontrada.');
    }
}