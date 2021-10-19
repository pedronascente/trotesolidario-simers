<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\admin\models\Doacao;
use app\modules\admin\models\DoacaoAdministratorSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\modules\admin\models\Helper;
use yii\filters\AccessControl;

/**
 * DoacaoController implements the CRUD actions for Doacao model.
 */
class DoacaoController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete','create','index','update'],
                'rules' => [
                    [
                        'actions' => ['delete','create','index','update'],
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
    public function actionIndex() {
        $this->layout = 'adminsemjquery';
        $searchModel = new DoacaoAdministratorSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    
    /**
     * Creates a new Doacao model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        
        if(strtotime(date('d-m-Y'))>=strtotime('16-10-'.date('Y')))
        {
            return $this->redirect(['index']);
        }
        $model = new Doacao();
        $model->ativo = 1;
        $this->layout = 'adminsemjquery';
        
        $erro = $config = array();
        // Tamanho máximo do arquivo (em bytes) 
        $config["tamanho"] = 2000000;

        // Largura máxima (pixels) 
        $config["largura"] = 640;

        // Altura máxima (pixels) 
        $config["altura"] = 640;

        //Extensão permitida
        $arr_extensao = array("image/jpeg", "image/gif", "image/png");
        if ($model->load(Yii::$app->request->post())) {
            $arquivo = UploadedFile::getInstance($model, 'file');
            $ultimo_id = Doacao::find()->select('id')->limit('1')->orderBy(['id' => SORT_DESC])->one();
            $model->user_update = Yii::$app->user->identity->id;
            $model->data_create = date('Y-m-d H:i:s');
            $model->data_update = date('Y-m-d H:i:s');
            
            if ($arquivo) {
                if (!in_array($arquivo->type, $arr_extensao)) {
                    $erro[] = "Arquivo em formato inválido! A imagem deve ser jpg, jpeg, gif ou png. Envie outro arquivo";
                }

                if ($arquivo->size > $config["tamanho"]) {
                    $erro[] = "Arquivo em tamanho muito grande! A imagem deve ser de no máximo " . $config["tamanho"] . " bytes. Envie outro arquivo";
                }

                // Para verificar as dimensões da imagem 
                $tamanhos = getimagesize($arquivo->tempName);

                // Verifica largura 
                if ($tamanhos[0] > $config["largura"]) {
                    $erro[] = "Largura da imagem não deve ultrapassar " . $config["largura"] . " pixels. Largura Atual: " . $tamanhos[0];
                }

                // Verifica altura 
                if ($tamanhos[1] > $config["altura"]) {
                    $erro[] = "Altura da imagem não deve ultrapassar " . $config["altura"] . " pixels. Altura Atual: " . $tamanhos[1];
                }

                $str_erro = '';
                foreach ($erro as $value) {
                    $str_erro .= $value . ' <br>';
                }


//                if ($erro) {
//                    return $this->render('create', [
//                                'model' => $model,
//                                'error' => true,
//                                'success' => false,
//                                'msg' => $str_erro
//                    ]);
//                }
                $name = ((!$ultimo_id) ? "1" : $ultimo_id->id + 1) . '_doacao.' . explode(".", $arquivo->name)[1];
                
                $path = Yii::$app->basePath . '/web/imagens/doacoes/' . $name;
                
                $arquivo->saveAs($path);
                $model->arquivo = $name;
            }
            if (!$model->save()) {
                return $this->render('create', [
                            'model' => $model,
                            'error' => true,
                            'success' => false,
                            'msg' => 'Erro ao criar doação'
                ]);
            }
            $html = '';
            $html .= "<p>Nome: ".Yii::$app->user->identity->name."</p><br>";
            $html .= "<p>Universidade: ".Yii::$app->user->identity->instituicao."</p><br>";
            $html .= "<p>Outra Universidade: ".Yii::$app->user->identity->outraInstituicao."</p><br>";
            $html = "<p>Acesse o link abaixo para acessar o arquivo</p><br>"; 
            $html .= "<a href='".Url::base(true)."/imagens/doacoes/$model->arquivo'>Clique aqui para acessar o arquivo.</a>"; 


            Yii::$app->mailer->compose('layouts/html', ['content' => $html])
                        ->setFrom('noreply@simers.org.br')
                        ->setTo('nucleoacademico@simers.org.br')
                        ->setSubject('Nova Doação')
                        ->send();
            
            return $this->render('update', [
                        'model' => $model,
                        'success' => true,
                        'error' => false,
                        'msg' => 'Doação criada com sucesso'
            ]);
        }

        return $this->render('create', [
                    'model' => $model,
                    'success' => false,
                    'error' => false,
                    'msg' =>''
        ]);
    }

    /**
     * Updates an existing Doacao model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        if(strtotime(date('d-m-Y'))>=strtotime('16-10-'.date('Y')))
        {
            return $this->redirect(['index']);
        }
        $model = $this->findModel($id);
        $this->layout = 'adminsemjquery';
        $this->layout = 'adminsemjquery';
        
        $erro = $config = array();
        // Tamanho máximo do arquivo (em bytes) 
        $config["tamanho"] = 2000000;

        // Largura máxima (pixels) 
        $config["largura"] = 640;

        // Altura máxima (pixels) 
        $config["altura"] = 640;

        //Extensão permitida
        $arr_extensao = array("image/jpeg", "image/gif", "image/png");
        if ($model->load(Yii::$app->request->post())) {
            $arquivo = UploadedFile::getInstance($model, 'file');
            $model->user_update = Yii::$app->user->identity->id;
            $model->data_update = date('Y-m-d H:i:s');
            
            if ($arquivo) {
                if (!in_array($arquivo->type, $arr_extensao)) {
                    $erro[] = "Arquivo em formato inválido! A imagem deve ser jpg, jpeg, gif ou png. Envie outro arquivo";
                }

                if ($arquivo->size > $config["tamanho"]) {
                    $erro[] = "Arquivo em tamanho muito grande! A imagem deve ser de no máximo " . $config["tamanho"] . " bytes. Envie outro arquivo";
                }

                // Para verificar as dimensões da imagem 
                $tamanhos = getimagesize($arquivo->tempName);

                // Verifica largura 
                if ($tamanhos[0] > $config["largura"]) {
                    $erro[] = "Largura da imagem não deve ultrapassar " . $config["largura"] . " pixels. Largura Atual: " . $tamanhos[0];
                }

                // Verifica altura 
                if ($tamanhos[1] > $config["altura"]) {
                    $erro[] = "Altura da imagem não deve ultrapassar " . $config["altura"] . " pixels. Altura Atual: " . $tamanhos[1];
                }

                $str_erro = '';
                foreach ($erro as $value) {
                    $str_erro .= $value . ' <br>';
                }


//                if ($erro) {
//                    return $this->render('create', [
//                                'model' => $model,
//                                'error' => true,
//                                'success' => false,
//                                'msg' => $str_erro
//                    ]);
//                }
                $name = $model->id . '_doacao.' . explode(".", $arquivo->name)[1];
                
                $path = Yii::$app->basePath . '/web/imagens/doacoes/' . $name;
                
                $arquivo->saveAs($path);
                $model->arquivo = $name;
            }
            if (!$model->save()) {
                return $this->render('update', [
                            'model' => $model,
                            'error' => true,
                            'success' => false,
                            'msg' => 'Erro ao atualizar doação'
                ]);
            }

            return $this->render('update', [
                        'model' => $model,
                        'success' => true,
                        'error' => false,
                        'msg' => 'Doação atualizada com sucesso'
            ]);
        }

        return $this->render('update', [
                    'model' => $model,
                    'success' => false,
                    'error' => false,
                    'msg' =>''
        ]);
    }

    /**
     * Deletes an existing Doacao model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->ativo = ($model->ativo == 1) ? 0 : 1;
        
        if (!$model->save()) {
            Helper::d($model->getErrors());
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }
    
    public function actionCheck() {
        $model = $this->findModel($_POST["model_id"]);
        $model->validado = ($model->validado == 1) ? 0 : 1;
        
        if (!$model->save()) {
            Helper::d($model->getErrors());
            return false;
        }
        $return = [];
        $return = ($model->validado == 1) ? ["far fa-check-square ", "Desaprovar"] : ["far fa-square ", "Aprovar"];
        
        return json_encode($return);
    }
    
    public function actionAtualizamotivo() {
        
        $model = $this->findModel($_POST["model_id"]);
        $model->validado_motivo = $_POST["texto"];
        
        if (!$model->save()) {
            Helper::d($model->getErrors());
            return false;
        }
        return true;
    }

    /**
     * Finds the Doacao model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Doacao the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Doacao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
