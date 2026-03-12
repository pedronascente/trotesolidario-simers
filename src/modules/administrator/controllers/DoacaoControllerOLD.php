<?php

namespace app\modules\administrator\controllers;

use Yii;
use app\modules\common\models\Doacao;
use app\modules\common\models\DoacaoUsersAdministratorSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\modules\common\models\Helper;
use app\modules\common\models\Users;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\helpers\Html;

class DoacaoController extends Controller{
 
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete', 'create', 'index', 'update'],
                'rules' => [
                    [
                        'actions' => ['delete', 'create', 'index', 'update', 'import'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //                    'logout' => ['post'],
                    'check' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(){
        $this->layout = 'adminsemjquery';
        $searchModel = new DoacaoUsersAdministratorSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionImport(){

        $this->layout = 'adminsemjquery';
        $model = new Doacao();
        $user = new Users();
        $tipoDoacao = '';
        $trote = '';

        if ($model->load(Yii::$app->request->post())) {
            $trote = $model->trote_id;
            $tipoDoacao = $model->tipo_doacao;


            $allowedFileType = [
                'application/vnd.ms-excel',
                'text/xls',
                'text/xlsx',
                'text/csv',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];

            $file = UploadedFile::getInstance($model, 'file');
            $comprovante = UploadedFile::getInstance($model, 'comprovante');


            if (in_array($file->type, $allowedFileType)) {
                $name = strtotime(date('Y-m-d H:i:s')) . "." . explode("/", $comprovante->type)[1];
                $path = Yii::$app->basePath . '/web/uploads/' . $name;
                $comprovante->saveAs($path);

                $file_array = [];
                if (($h = fopen($file->tempName, "r")) !== FALSE) {

                    while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
                        array_push($file_array, $data);
                    }

                    //if ($file_array);
                }

                unset($file_array[0]);
                foreach ($file_array as $email) {


                    if ($usuario = $user::findByEmail($email[0])) {
                        $model = new Doacao();
                        //user
                        $model->user_create = $usuario->id;
                        $model->instituicao = !$usuario->instituicao ? 21 : $usuario->instituicao;
                        //doacao
                        $model->trote_id = $trote;
                        $model->arquivo = $name;
                        $model->tipo_doacao = $tipoDoacao;
                        $model->validado = 1;
                        $model->usuario_validacao = Yii::$app->user->identity->id;
                        $model->data_create = date('Y-m-d H:i:s');
                        $model->ativo = 1;
                        if (!$model->save()) {
                            Helper::d($model->getErrors());
                        }
                    }
                }
            }
        }
        return $this->render('import', [
            'model' => $model

        ]);
    }

    public function actionCreate(){
        $model = new Doacao();
        $model->ativo = 1;
        $this->layout = 'adminsemjquery';

        $erro = $config = array();
        $config["tamanho"] = 3000000;
        $config["largura"] = 640;
        $config["altura"] = 640;

        $arr_extensao = array("image/jpeg", "image/gif", "image/png");
        if ($model->load(Yii::$app->request->post()) && !empty($model)) {
            $arquivo = UploadedFile::getInstance($model, 'file');
            $model->user_update = Yii::$app->user->identity->id;
            $model->data_create = date('Y-m-d H:i:s');
            $model->data_update = date('Y-m-d H:i:s');

            if ($arquivo) {
                // Verifica e cria o diretório se não existir
                $uploadDir = Yii::$app->basePath . '/web/imagens/doacoes/';
                if (!file_exists($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true)) {
                        Yii::error("Falha ao criar diretório de upload: " . $uploadDir);
                        return $this->render('create', [
                            'model' => $model,
                            'error' => true,
                            'success' => false,
                            'msg' => 'Erro ao criar diretório para upload'
                        ]);
                    }
                }

                if (!in_array($arquivo->type, $arr_extensao)) {
                    $erro[] = "Arquivo em formato inválido! A imagem deve ser jpg, jpeg, gif ou png. Envie outro arquivo";
                }

                if ($arquivo->size > $config["tamanho"]) {
                    $erro[] = "Arquivo em tamanho muito grande! A imagem deve ser de no máximo " . $config["tamanho"] . " bytes. Envie outro arquivo";
                }

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

                $name = strtotime(date('Y-m-d H:i:s')) . "." . explode("/", $arquivo->type)[1];
                $path = $uploadDir . $name;

                if (!$arquivo->saveAs($path)) {
                    Yii::error("Falha ao salvar arquivo: " . $path);
                    return $this->render('create', [
                        'model' => $model,
                        'error' => true,
                        'success' => false,
                        'msg' => 'Erro ao salvar arquivo'
                    ]);
                }
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
            $html .= "<p>Nome: " . Yii::$app->user->identity->name . "</p><br>";
            $html .= "<p>Universidade: " . Yii::$app->user->identity->instituicao . "</p><br>";
            $html .= "<p>Outra Universidade: " . Yii::$app->user->identity->outraInstituicao . "</p><br>";
            $html = "<p>Acesse o link abaixo para acessar o arquivo</p><br>";
            $html .= "<a href='" . Url::base(true) . "/imagens/doacoes/$model->arquivo'>Clique aqui para acessar o arquivo.</a>";


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
            'msg' => ''
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
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

                $name = strtotime(date('Y-m-d H:i:s')) . "." . explode("/", $arquivo->type)[1];
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
            'msg' => ''
        ]);
    }

    public function actionDelete($id){
        $model = $this->findModel($id);
        $model->ativo = ($model->ativo == 1) ? 0 : 1;

        if (!$model->save()) {
            Helper::d($model->getErrors());
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }

    public function actionValidar($id, $status){
        $model = $this->findModel($id);
        $model->validado = (int) $status;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Status atualizado com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao salvar.');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    public function actionCheck(){
        $model = $this->findModel($_POST["model_id"]);
        $novoValor = $_POST["novo_valor"] ?? null;

        if ($novoValor !== null) {
            $model->validado = (int) $novoValor;
        }
        if (!$model->save()) {
            Yii::$app->response->statusCode = 500;
            return json_encode(['error' => 'Erro ao salvar', 'details' => $model->getErrors()]);
        }

        return json_encode(['success' => true]);
    }

    public function actionAtualizamotivo(){

        $model = $this->findModel($_POST["model_id"]);
        $model->validado_motivo = $_POST["texto"];

        if (!$model->save()) {
            Helper::d($model->getErrors());
            return false;
        }
        return true;
    }

    protected function findModel($id){
        if (($model = Doacao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
