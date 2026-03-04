<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\Users;
use app\modules\common\models\UsersSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'update', 'view', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'adminsemjquery';
        $searchModel = new UsersSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
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
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = 'adminsemjquery';
        $model = new Users();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->passwordHash) {
                $model->setPassword($model->passwordHash);
            } else {
                $model->passwordHash = $model->oldAttributes['passwordHash'];
            }
            $model->generateAuthKey();
            $model->generatePasswordResetToken();
            $model->setCreated();
            $model->setUpdated();
            $model->status = Users::STATUS_ACTIVE;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Usuário criado com sucesso');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao salvar o usuário');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->layout = 'adminsemjquery';
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->passwordHash);
            $model->setUpdated();

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Usuário editado com sucesso');
                // return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao salvar o usuário');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }


        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->layout = 'adminsemjquery';
        $model = $this->findModel($id);

        $model->status = ($model->status == Users::STATUS_ACTIVE) ? Users::STATUS_DELETED : Users::STATUS_ACTIVE;

        $model->setUpdated();

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Usuário editado com sucesso');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao salvar o usuário');
            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
