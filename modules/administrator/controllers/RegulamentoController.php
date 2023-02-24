<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\participante\models\Regulamento;
use app\modules\participante\models\RegulamentoSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * RegulamentoController implements the CRUD actions for Regulamento model.
 */
class RegulamentoController extends Controller
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

    /**
     * Lists all Regulamento models.
     * @return mixed
     */
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

    /**
     * Displays a single Regulamento model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Regulamento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
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

    /**
     * Updates an existing Regulamento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->layout = 'adminsemjquery';

        if ($model->load(Yii::$app->request->post())) {
            $arquivo = UploadedFile::getInstance($model, 'file');
            if ($arquivo) {
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

    /**
     * Deletes an existing Regulamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Regulamento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Regulamento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Regulamento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
