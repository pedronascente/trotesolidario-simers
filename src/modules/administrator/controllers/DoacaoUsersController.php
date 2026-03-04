<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\Doacao;
use app\modules\common\models\DoacaoAdministratorSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\modules\common\models\Helper;
use yii\filters\AccessControl;
use \app\modules\common\models\DoacaoUsersAdministratorSearchModel;

/**
 * DoacaoController implements the CRUD actions for Doacao model.
 */
class DoacaoUsersController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
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
     * Lists all Doacao models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'adminsemjquery';
        $searchModel = new DoacaoUsersAdministratorSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
