<?php

namespace app\modules\administrator\controllers;
 
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\modules\common\models\Documento;
use app\modules\common\models\DocumentoSearchModel;
use app\modules\common\services\contracts\DocumentoServiceInterface;

class RegulamentoController extends Controller{

    private DocumentoServiceInterface $service;

    public function __construct(
        $id,
        $module,
        DocumentoServiceInterface $service,
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

    public function actionIndex()
    {
        $searchModel = new DocumentoSearchModel();

        $params = Yii::$app->request->queryParams;
        $params['DocumentoSearchModel']['tipo'] = \app\modules\common\models\Documento::TIPO_REGULAMENTO;
        $dataProvider = $searchModel->search($params);
        $this->layout = 'adminsemjquery';

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = Documento::find()
            ->where([
                'id' => $id,
                'tipo' => Documento::TIPO_REGULAMENTO
            ])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Regulamento não encontrado.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate(){

        $this->layout = 'adminsemjquery';
        $regulamento = new Documento();

        if ($regulamento->load(Yii::$app->request->post())) {
            if($this->service->create($regulamento)){
                Yii::$app->session->setFlash('success', 'Regulamento criado com sucesso');
                return $this->redirect(['index']);
            }
            Yii::$app->session->setFlash('error', 'Erro ao criar Regulamento');
        }

        return $this->render('create', [
            'model' => $regulamento
        ]);
    }

    public function actionUpdate($id){
        $this->layout = 'adminsemjquery';
        $regulamento = $this->findModel($id);

        if ($regulamento->load(Yii::$app->request->post())) {

            if ($this->service->update($regulamento)) {
                Yii::$app->session->setFlash('success', 'Regulamento atualizado com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao atualizar Regulamento');
        }

        return $this->render('update', [
            'model' => $regulamento
        ]);
    }

    public function actionDelete($id){
        $this->layout = 'adminsemjquery';
        $regulamento = $this->findModel($id);

        if ($this->service->delete($regulamento)) {
            Yii::$app->session->setFlash('success', 'Regulamento excluído com sucesso');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao excluir Regulamento');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id){
        if (($model = Documento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}