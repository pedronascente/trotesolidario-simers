<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\Universidade;
use app\modules\common\models\UniversidadeSearchModel;
use app\modules\common\models\Trote;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

class UniversidadeController extends Controller{

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
        $searchModel = new UniversidadeSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $trotes = ArrayHelper::map(
            Trote::find()
                ->where(['ativo' => 1])
                ->orderBy('nome')
                ->all(),
            'id',
            'nome'
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'trotes' => $trotes,
        ]);
    }

    public function actionView($id){
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate(){ 
        $this->layout = 'adminsemjquery';
        $model = new Universidade();

        if ($model->load(Yii::$app->request->post())) {

            $arquivo = UploadedFile::getInstance($model, 'file');

            if ($arquivo) {
                $dir = Yii::getAlias('@webroot/img/');
                if (!is_dir($dir)) mkdir($dir, 0775, true);

                $model->icon = uniqid('uni_') . '.' . $arquivo->extension;
            }

            if ($model->validate() && $model->save(false)) {

                if ($arquivo) {
                    $arquivo->saveAs(Yii::getAlias('@webroot/img/') . $model->icon);
                }

                Yii::$app->session->setFlash('success', 'Universidade criada com sucesso');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao criar Universidade');
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id){
        $this->layout = 'adminsemjquery';
        $model = $this->findModel($id);
        $imagemAntiga = $model->icon;

        if ($model->load(Yii::$app->request->post())) {

            $arquivo = UploadedFile::getInstance($model, 'file');

            if ($arquivo) {
                $dir = Yii::getAlias('@webroot/img/');
                if (!is_dir($dir)) mkdir($dir, 0775, true);

                $nomeArquivo = uniqid('uni_') . '.' . $arquivo->extension;

                if ($arquivo->saveAs($dir . $nomeArquivo)) {
                    $model->icon = $nomeArquivo;

                    // remove arquivo antigo
                    if ($imagemAntiga && file_exists($dir . $imagemAntiga)) {
                        @unlink($dir . $imagemAntiga);
                    }
                }
            } else {
                // mantém imagem antiga se nenhum arquivo novo enviado
                $model->icon = $imagemAntiga;
            }

            if ($model->validate() && $model->save(false)) {
                Yii::$app->session->setFlash('success', 'Universidade atualizada com sucesso');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao atualizar Universidade');
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id){
        $model = $this->findModel($id);
        $model->ativo = ($model->ativo == 1) ? 0 : 1;

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Status alterado com sucesso');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao alterar status');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id){
        if (($model = Universidade::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Universidade não encontrada.');
    }
}
