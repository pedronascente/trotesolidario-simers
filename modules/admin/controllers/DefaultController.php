<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\modules\admin\models\LoginForm;
use app\modules\admin\models\Doacao;
use app\modules\admin\models\Helper;
use app\modules\admin\models\Users;
use app\modules\admin\models\Universidade;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'home'],
                'rules' => [
                    [
                        'actions' => ['logout', 'home'],
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
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $universidades = Universidade::find()
            ->where(['ativo' => 1])
            ->andWhere(['<=', 'id', '20'])->all();

        $this->layout = 'adminindex';
        if (!Yii::$app->user->isGuest) {

            return $this->redirect(['default/home']);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['default/home']);
        }
        $this->layout = 'login';
        return $this->render('index', [
            'model' => $model,
            'universidades_botoes' => $universidades,
        ]);
    }

    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['default/home']);
        }

        $model = new LoginForm();

        if (Yii::$app->request->post()) {
            return $this->redirect(['default/home']);
        }
        $this->layout = 'register';
        return $this->render('register', ['model' => $model]);
    }

    public function actionHome()
    {
        $this->layout = 'adminindex';
        if (Yii::$app->user->identity->trote_id == 1) {

            return $this->redirect(['users/perfil']);
        }

        $universidades = Universidade::find()
            ->where(['ativo' => 1])
            ->andWhere(['<=', 'id', '20'])->all();

        $dados = [];

        $array_cores = [
            '#B0E0E6',
            '#D8BFD8',
            '#EEE8AA',
            '#FAEBD7',
            '#FFD700',
            '#FF6347',
            '#FF69B4',
            '#8A2BE2',
            '#2E8B57',
            '#008B8B',
            '#483D8B',
            '#4F4F4F',
            '#FFDEAD',
            '#DA70D6',
            '#CD5C5C',
            '#FF7F50',
            '#FFFF00',
            '#FFE4C4',
            '#E6E6FA',
            '#F5FFFA'
        ];
        foreach ($universidades as $universidade) {
            $dados_universidade = Users::find()
                ->innerJoin('_trote', '_users.trote_id = _trote.id')
                ->where([
                    '_users.trote_id' => 2,
                    '_users.status' => 1,
                    '_users.estudante' => 'Sim',
                    '_users.instituicao' => $universidade->id
                ])
                ->all();

            array_push($dados, [
                "name" => $universidade->nome,
                "points" => count($dados_universidade),
                "color" => $array_cores[array_rand($array_cores)],
                "bullet" => "/img/{$universidade->icon}"
            ]);
        }


        return $this->render('home', [
            'universidades_botoes' => $universidades,
            'dados' => json_encode($dados)
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['/admin']);
    }
}
