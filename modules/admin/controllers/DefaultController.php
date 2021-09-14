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

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller {

    public $enableCsrfValidation = false;

    public function behaviors() {
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
    public function actionIndex() {
        $this->layout = 'adminindex';
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['default/home']);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['default/home']);
        }
        $this->layout = 'login';
        return $this->render('index', ['model' => $model]);
    }

    public function actionRegister() {
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

    public function actionHome() {
        $this->layout = 'adminindex';
        $ufrgs_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UFRGS - Universidade Federal do Rio Grande do Sul'])->all();
        $ulbra_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'ULBRA - Universidade Luterana do Brasil'])->all();
        $unisinos_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UNISINOS - Universidade do Vale do Rio dos Sinos'])->all();
        $ucs_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UCS - Universidade de Caxias do Sul'])->all();
        $upf_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UPF - Universidade de Passo Fundo'])->all();
        $uffs_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UFFS - Universidade Federal da Fronteira do Sul'])->all();
        $ufpel_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UFPEL - Universidade Federal de Pelotas'])->all();
        $ufsm_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UFSM - Universidade Federal de Santa Maria'])->all();
        $ufn_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UFN - Universidade Franciscana'])->all();
        $univates_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UNIVATES - Fundação Vale do Taquari'])->all();
        $unisc_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UNISC - Universidade de Santa Cruz'])->all();
        $unipampa_dados = Users::find()->where(['status' => 1, 'estudante' => 'Sim', 'instituicao' => 'UNIPAMPA - Universidade Federal do Pampa'])->all();

        $dados = json_encode([
            [
                "name" => "UFRGS",
                "points" => count($ufrgs_dados),
                "color"=> '#800000',
                "bullet" => "/img/logos_300x300_ufrgs.png"
            ],
            [
                "name" => "ULBRA",
                "points" => count($ulbra_dados),
                "color"=> '#800080',
                "bullet" => "/img/logos_300x300_ulbra.png"
            ],
            [
                "name" => "UNISINOS",
                "points" => count($unisinos_dados),
                "color"=> '#000080',
                "bullet" => "/img/logos_300x300_unisinos.png"
            ],
            [
                "name" => "UCS",
                "points" => count($ucs_dados),
                "color"=> '#008080',
                "bullet" => "/img/logos_300x300_UCS.png"
            ],
            [
                "name" => "UPF",
                "points" => count($upf_dados),
                "color"=> '#CCCCFF',
                "bullet" => "/img/logos_300x300_UPF.png"
            ],
            [
                "name" => "UFFS",
                "points" => count($uffs_dados),
                "color"=> '#6495ED',
                "bullet" => "/img/logos_300x300_UFFS.png"
            ],
            [
                "name" => "UFPEL",
                "points" => count($ufpel_dados),
                "color"=> '#40E0D0',
                "bullet" => "/img/logos_300x300_UFPEL.png"
            ],
            [
                "name" => "UFSM",
                "points" => count($ufsm_dados),
                "color"=> '#9FE2BF',
                "bullet" => "/img/logos_300x300_ufsm.png"
            ],
            [
                "name" => "UFN",
                "points" => count($ufn_dados),
                "color"=> '#DE3163',
                "bullet" => "/img/logos_300x300_UFN.png"
            ],
            [
                "name" => "UNIVATES",
                "points" => count($univates_dados),
                "color"=> '#FF7F50',
                "bullet" => "/img/logos_300x300_univates.png"
            ],
            [
                "name" => "UNISC",
                "points" => count($unisc_dados),
                "color"=> '#FFBF00',
                "bullet" => "/img/logos_300x300_unisc.png"
            ],
            [
                "name" => "UNIPAMPA",
                "points" => count($unipampa_dados),
                "color"=> '#DFFF00',
                "bullet" => "/img/logos_300x300_unimpa.png"
            ]
        ]);


        return $this->render('home', [
                    'dados' => $dados
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->redirect(['/admin']);
    }

}
