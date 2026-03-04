<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\modules\common\models\Banner;
use app\modules\common\models\BannerSearchModel;
use app\modules\common\services\contracts\BannerServiceInterface;

class BannerController extends Controller{

    private BannerServiceInterface $service;

    public function __construct($id,$module,BannerServiceInterface $service,$config = []) {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors(): array{
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
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

        $searchModel  = new BannerSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider'=> $dataProvider,
        ]);
    }

    public function actionView($id){

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate(){

        $this->layout = 'adminsemjquery';
        $banner = new Banner();

        if ($banner->load(Yii::$app->request->post())) {

            if ($this->service->create($banner)) {
                Yii::$app->session->setFlash('success', 'Banner criado com sucesso.');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao criar banner.');
        }

        return $this->render('create', [
            'model' => $banner,
        ]);
    }

    public function actionUpdate($id){

        $this->layout = 'adminsemjquery';
        $banner = $this->findModel($id);

        if ($banner->load(Yii::$app->request->post())) {

            if ($this->service->update($banner)) {
                Yii::$app->session->setFlash('success', 'Banner atualizado com sucesso.');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao atualizar banner.');
        }

        return $this->render('update', [
            'model' => $banner,
        ]);
    }

    public function actionDelete($id){

        $banner = $this->findModel($id);

        if ($this->service->delete($banner)) {
            Yii::$app->session->setFlash('success', 'Banner excluído com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao excluir banner.');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id): Banner{

        if (($model = Banner::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Banner não encontrado.');
    }
}
