<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\common\models\Documento;
use app\modules\common\models\DocumentoSearchModel;
use app\modules\common\services\contracts\DocumentoServiceInterface;

class InformativoController extends Controller
{

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

    public function behaviors()
    {
        return [
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
        $searchModel = new DocumentoSearchModel();

        $params = Yii::$app->request->queryParams;
        $params['DocumentoSearchModel']['tipo'] = \app\modules\common\models\Documento::TIPO_INFORMATIVO;
        $dataProvider = $searchModel->search($params);
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
                'tipo' => Documento::TIPO_INFORMATIVO
            ])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Informativo não encontrado.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $informativo = new Documento();
        
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

    public function actionUpdate($id)
    {
        $informativo = $this->findModel($id);
       
        if($informativo->load(Yii::$app->request->post()))
        {
            if($this->service->update($informativo))
            {
                Yii::$app->session->setFlash('success', 'Informativo atualizada com sucesso');
                return $this->redirect(['index']);
            }  
            Yii::$app->session->setFlash('error', 'Erro ao atualizar Informativo');          
        }  
        return $this->render('update', [
            'model' => $informativo,
        ]);
    }

    public function actionDelete($id)
    {
        $informativo = $this->findModel($id);
      
        if($this->service->delete($informativo))
        {
             Yii::$app->session->setFlash('success', 'Informativo excluído com sucesso');
        } else {
             Yii::$app->session->setFlash('error', 'Erro ao excluir Informativo');
        }
        return $this->redirect(['index']);    
    }

    protected function findModel($id)
    {
        if (($informativo = Documento::findOne($id)) !== null) 
        {
            return $informativo;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
