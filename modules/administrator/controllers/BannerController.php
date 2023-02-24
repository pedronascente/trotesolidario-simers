<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\participante\models\Banner;
use app\modules\participante\models\BannerSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * BannerController implements the CRUD actions for Banner model.
 */
class BannerController extends Controller
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
     * Lists all Banner models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BannerSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->layout = 'adminsemjquery';
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Banner model.
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
     * Creates a new Banner model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Banner();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Banner model.
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
            $arquivo_dsk = UploadedFile::getInstance($model, 'file_dsk');
            $arquivo_mob = UploadedFile::getInstance($model, 'file_mob');
            if ($arquivo_dsk) {
                $path = Yii::$app->basePath . '/web/img/' . $model->img_dsk;
                $arquivo_dsk->saveAs($path);
            }
            if ($arquivo_mob) {
                $path = Yii::$app->basePath . '/web/img/' . $model->img_mob;
                $arquivo_mob->saveAs($path);
            }
            if (!$model->save()) {
                return $this->render('update', [
                    'model' => $model,
                    'error' => true,
                    'success' => false,
                    'msg' => 'Erro ao atualizar Banner'
                ]);
            }

            return $this->render('update', [
                'model' => $model,
                'success' => true,
                'error' => false,
                'msg' => 'Banner atualizada com sucesso'
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
     * Deletes an existing Banner model.
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
     * Finds the Banner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Banner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Banner::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
