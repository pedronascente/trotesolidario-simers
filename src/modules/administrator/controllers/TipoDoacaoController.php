<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\modules\common\models\TipoDoacao;
use app\modules\common\models\TipoDoacaoSearchModel;
use app\modules\common\services\contracts\TipoDoacaoServiceInterface;

class TipoDoacaoController extends Controller
{
    private TipoDoacaoServiceInterface $service;

    public function __construct(
        $id,
        $module,
        TipoDoacaoServiceInterface $service,
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

    /**
     * Define layout padrão antes de qualquer action
     */
    public function beforeAction($action)
    {
        $this->layout = 'adminsemjquery';
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new TipoDoacaoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $tipoDoacao = new TipoDoacao();

        if ($tipoDoacao->load(Yii::$app->request->post()))
        {
            if ($this->service->create($tipoDoacao)) 
            {
                Yii::$app->session->setFlash('success', 'TipoDoacao criado com sucesso');
                return $this->redirect(['index']);
            }
            Yii::$app->session->setFlash('error', 'Erro ao criar TipoDoacao');
        }

        return $this->render('create', [
            'model' => $tipoDoacao
        ]);
    }

    public function actionUpdate($id)
    {
        $tipoDoacao = $this->findModel($id);

        if ($tipoDoacao->load(Yii::$app->request->post())) 
        {
            if ($this->service->update($tipoDoacao)) 
            {
                Yii::$app->session->setFlash('success', 'TipoDoacao atualizada com sucesso');
                return $this->redirect(['index']);
            }
            Yii::$app->session->setFlash('error', 'Erro ao atualizar TipoDoacao');
        }

        return $this->render('update', [
            'model' => $tipoDoacao,
        ]);
    }

    public function actionDelete($id)
    {
        $tipoDoacao = $this->findModel($id);

        if ($this->service->delete($tipoDoacao)) 
        {
            Yii::$app->session->setFlash('success', 'TipoDoacao excluído com sucesso');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao excluir TipoDoacao');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id): TipoDoacao
    {
        if (($tipoDoacao = TipoDoacao::findOne($id)) !== null) 
        {
            return $tipoDoacao;
        }

        throw new NotFoundHttpException('TipoDoacao não encontrado.');
    }
}
