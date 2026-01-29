<?php

namespace app\modules\administrator\controllers;

use app\modules\participante\models\Helper;
use Yii;
use app\modules\participante\models\Universidade;
use app\modules\participante\models\UniversidadeSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;


class UniversidadeController extends Controller
{
    /**
     * {@inheritdoc}
     */
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
        $this->layout = 'adminsemjquery';
        $searchModel = new UniversidadeSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        $model = new Universidade();
        $this->layout = 'adminsemjquery';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionUpdate($id)
    {
        $this->layout = 'adminsemjquery';
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $arquivo = UploadedFile::getInstance($model, 'file');
            if ($arquivo) {
                $path = Yii::$app->basePath . '/web/img/' . $model->icon;
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
        if (($model = Universidade::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
