<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Users;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\admin\models\Helper;
use yii\filters\AccessControl;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['perfil'],
                'rules' => [
                    [
                        'actions' => ['perfil'],
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

    public function actionPerfil() {
        $model = Users::findOne(Yii::$app->user->identity->id);
        $model->passwordHash = '';


        $this->layout = 'adminsemjquery';
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            //Valida se o e-mail ja existe
            if ($model->oldAttributes['email'] != $model->attributes['email']) {
                $usuario = Users::findByEmail($model->email);
                if ($usuario) {
                    $msg = 'Endereço de email já esta sendo utilizado';
                    return $this->render('index', [
                                'model' => $model,
                                'error' => true,
                                'msg' => $msg
                    ]);
                }
            }
            //Valida se a senha vem preenchida e atualiza o hash
            if($model->passwordHash){
                $model->setPassword($model->passwordHash);
            }else{
                $model->passwordHash = $model->oldAttributes['passwordHash'];
            }
            //Salva a data de atualização
            $model->setUpdated();
            
            if ($model->estudante == 'Sim') {
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
                    return $this->render('perfil', [
                                'model' => $model,
                                'error' => true,
                                'msg' => $msg
                    ]);
                }

                //Verifica se marcou o campo telefone
                if (!$model->telefone) {
                    $msg = 'Você precisa preencher o telefone';
                    return $this->render('perfil', [
                                'model' => $model,
                                'error' => true,
                                'msg' => $msg
                    ]);
                }
                //Verifica se marcou previsão de formatura
                if (!$model->previsaoFormatura) {
                    $msg = 'Você precisa preencher a previsão de formatura';
                    return $this->render('perfil', [
                                'model' => $model,
                                'error' => true,
                                'msg' => $msg
                    ]);
                }
            }else{
                $model->instituicao = '';
                $model->outraInstituicao = '';
                $model->telefone = '';
                $model->previsaoFormatura = '';
            }
            
            

            if ($model->save()) {
                return $this->render('perfil', [
                            'model' => $model,
                            'success' => true,
                            'error' => false,
                            'msg' => 'Perfil atualizado com sucesso'
                ]);
            } else {
                return $this->render('perfil', [
                            'model' => $model,
                            'error' => true,
                            'success' => false,
                            'msg' => 'Erro ao atualizar perfil'
                ]);
            }
        }

        return $this->render('perfil', [
                    'model' => $model,
                    'success' => false,
                    'error' => false,
        ]);
    }

    
    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
