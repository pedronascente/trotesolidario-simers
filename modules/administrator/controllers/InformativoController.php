<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\Informativo;
use app\modules\common\models\InformativoSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\db\ActiveRecord; 
class InformativoController extends Controller
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
        $searchModel = new InformativoSearchModel();
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
        $this->layout = 'adminsemjquery';
        $model = new Informativo();

        if ($model->load(Yii::$app->request->post())) {

            if ($this->handleUpload($model) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Informativo criado com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao criar Informativo');
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    public function actionUpdate($id)
    {
        $this->layout = 'adminsemjquery';
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if ($this->handleUpload($model) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Informativo atualizado com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao atualizar Informativo');
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionDelete($id)
    {
        $this->layout = 'adminsemjquery';
        $model = $this->findModel($id);

        // caminho do arquivo (se existir)
        $filePath = Yii::$app->basePath . '/web/pdf/' . $model->arquivo;

        if ($model->delete()) {

            // remove o arquivo físico
            if (!empty($model->arquivo) && file_exists($filePath)) {
                @unlink($filePath);
            }

            Yii::$app->session->setFlash('success', 'Informativo excluído com sucesso');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao excluir Informativo');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Informativo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

   private function handleUpload(ActiveRecord $model): bool
    {
        $arquivo = UploadedFile::getInstance($model, 'file');

        if (!$arquivo) {
            return true; // upload não é obrigatório
        }

        $ext = $arquivo->getExtension();
        $fileName = md5(uniqid('', true)) . '.' . $ext;

        $model->arquivo = $fileName;

        $path = Yii::$app->basePath . '/web/pdf/' . $fileName;

        return $arquivo->saveAs($path);
    }
}

