<?php

namespace app\modules\participante\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\modules\participante\models\RegisterForm;
use app\modules\participante\models\Helper;
use app\modules\participante\models\Users;

/**
 * Default controller for the `participante` module
 */
class RegisterController extends Controller
{

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'home', 'perfil'],
                'rules' => [
                    [
                        'actions' => ['logout', 'home', 'perfil'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $msg = '';
        $this->layout = 'register';
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['default/home']);
        }

        $model = new RegisterForm();

        if ($model->load(Yii::$app->request->post())) {
            //Valida se o e-mail ja existe
            $usuario = Users::findByEmail($model->email);
            if ($usuario) {
                $msg = 'Endereço de email já esta sendo utilizado';
                return $this->render('index', [
                    'model' => $model,
                    'error' => true,
                    'msg' => $msg
                ]);
            }

            $post = Yii::$app->request->post()['RegisterForm'];

            //Verifica se é estudante
            if ($model->estudante == 'Sim') {

                $model->telefone = $post['telefone'];
                $model->outraInstituicao = $post["outraInstituicao"];
                $model->instituicao = $post["instituicao"];
                $model->previsaoFormatura = $post["previsaoFormatura"];
                $model->conheceONas = $post["conheceONas"];

                //Verifica se tem instituicao marcado
                if (!$model->instituicao) {
                    //Verifica marcou outra instituição e se outra instituição esta vazio
                    if ($model->instituicao == 'Outra' && !$model->outraInstituicao) {
                        $msg = 'Você precisa preencher a outra instituição';
                        return $this->render('index', [
                            'model' => $model,
                            'error' => true,
                            'msg' => $msg
                        ]);
                    }
                    $msg = 'Você precisa preencher a instituição';
                    return $this->render('index', [
                        'model' => $model,
                        'error' => true,
                        'msg' => $msg
                    ]);
                }

                //Verifica se marcou o campo telefone
                if (!$model->telefone) {
                    $msg = 'Você precisa preencher o telefone';
                    return $this->render('index', [
                        'model' => $model,
                        'error' => true,
                        'msg' => $msg
                    ]);
                }
                //Verifica se marcou previsão de formatura
                if (!$model->previsaoFormatura) {
                    $msg = 'Você precisa preencher a previsão de formatura';
                    return $this->render('index', [
                        'model' => $model,
                        'error' => true,
                        'msg' => $msg
                    ]);
                }

                //Verifica se conhece o nas
                if (!$model->conheceONas) {
                    $msg = 'Você precisa preencher se conhece o NAS';
                    return $this->render('index', [
                        'model' => $model,
                        'error' => true,
                        'msg' => $msg
                    ]);
                }

                $modelUsuario = new Users;
                $post = Yii::$app->request->post()['RegisterForm'];
                $modelUsuario->name = $post["name"];
                $modelUsuario->trote_id = $post["trote_id"];
                $modelUsuario->setPassword($post["password"]);
                $modelUsuario->email = $post["email"];
                $modelUsuario->status = 1;
                $modelUsuario->setCreated();
                $modelUsuario->generatePasswordResetToken();
                $modelUsuario->generateAuthKey();
                $modelUsuario->estudante = $post["estudante"];
                $modelUsuario->politicaPrivacidade = $post["politicaPrivacidade"];
                $modelUsuario->politicaImagem = $post["politicaImagem"];
                $modelUsuario->instituicao = isset($post["instituicao"]) ? $post["instituicao"] : null;
                $modelUsuario->outraInstituicao = $post["outraInstituicao"];
                $modelUsuario->telefone = $post["telefone"];
                $modelUsuario->previsaoFormatura = $post["previsaoFormatura"];
                $modelUsuario->conheceONas = $post["conheceONas"];
                $modelUsuario->cpf = $post["cpf"];
                $modelUsuario->estudanteMedicina = $post["estudanteMedicina"];
                $modelUsuario->estudanteOutros = $post["estudanteOutros"];
                if (!$modelUsuario->save()) {
                    $msg = 'Houve um erro no cadastro informe o NAS';
                    return $this->render('index', [
                        'model' => $model,
                        'error' => true,
                        'msg' => $msg
                    ]);
                }
            } else {
                $modelUsuario = new Users;
                $post = Yii::$app->request->post()['RegisterForm'];
                $modelUsuario->name = $post["name"];
                $modelUsuario->setPassword($post["password"]);
                $modelUsuario->email = $post["email"];
                $modelUsuario->status = 1;
                $modelUsuario->setCreated();
                $modelUsuario->generatePasswordResetToken();
                $modelUsuario->generateAuthKey();
                $modelUsuario->estudante = $post["estudante"];
                $modelUsuario->instituicao = isset($post["instituicao"]) ? $post["instituicao"] : null;
                $modelUsuario->politicaPrivacidade = $post["politicaPrivacidade"];
                $modelUsuario->politicaImagem = $post["politicaImagem"];
                $modelUsuario->cpf = $post["cpf"];
                if (!$modelUsuario->save()) {
                    $msg = 'Houve um erro no cadastro informe o NAS';
                    return $this->render('index', [
                        'model' => $model,
                        'error' => true,
                        'msg' => $msg
                    ]);
                }
            }

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->redirect(['default/home']);
            }
        }

        return $this->render('index', [
            'model' => $model,
            'error' => false,
            'msg' => $msg
        ]);
    }
}
