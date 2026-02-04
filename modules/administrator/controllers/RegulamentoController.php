<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\Regulamento;
use app\modules\common\models\RegulamentoSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class RegulamentoController extends Controller
{
   
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new RegulamentoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->layout = 'adminsemjquery';
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Regulamento();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->layout = 'adminsemjquery';

        if ($model->load(Yii::$app->request->post())) {
            $arquivo = UploadedFile::getInstance($model, 'file');
            if ($arquivo) {
                if (!empty($model->arquivo)) {
                    $oldPath = Yii::$app->basePath . '/web/pdf/' . $model->arquivo;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
    
                $newName = md5(uniqid(rand(), true)) . '.' . $arquivo->getExtension();
                $model->arquivo = $newName; 
    
                $path = Yii::$app->basePath . '/web/pdf/' . $model->arquivo;
                $arquivo->saveAs($path);
            }
            if (!$model->save()) {
                return $this->render('update', [
                    'model' => $model,
                    'error' => true,
                    'success' => false,
                    'msg' => 'Erro ao atualizar Universidade'
                ]);
            }

            return $this->render('update', [
                'model' => $model,
                'success' => true,
                'error' => false,
                'msg' => 'Universidade atualizada com sucesso'
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'success' => false,
            'error' => false,
            'msg' => ''
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Regulamento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
