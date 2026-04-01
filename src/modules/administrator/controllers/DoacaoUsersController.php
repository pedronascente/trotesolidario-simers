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

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // precisa estar logado
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    // não logado → login
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }

                    // logado mas não admin → 403
                    throw new \yii\web\ForbiddenHttpException('Acesso negado');
                }
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

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
