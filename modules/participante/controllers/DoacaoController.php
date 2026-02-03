<?php

namespace app\modules\participante\controllers;

use Yii;
use app\modules\common\models\Doacao;
use app\modules\common\models\DoacaoSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\modules\common\models\Helper;
use yii\filters\AccessControl;

/**
 * DoacaoController implements the CRUD actions for Doacao model.
 */
class DoacaoController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete', 'create', 'index', 'update'],
                'rules' => [
                    [
                        'actions' => ['delete', 'create', 'index', 'update'],
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
        if (Yii::$app->user->identity->trote_id == 1) {

            return $this->redirect(['users/perfil']);
        }
        $searchModel = new DoacaoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Corrige a orientação da imagem baseada nos dados EXIF
     * @param string $filepath
     * @return bool
     */
    private function corrigirOrientacaoImagem($filepath)
    {
        if (!function_exists('exif_read_data')) {
            return false;
        }

        try {
            $exif = @exif_read_data($filepath);

            if (!$exif || !isset($exif['Orientation'])) {
                return true;
            }

            $orientation = $exif['Orientation'];

            $imageType = exif_imagetype($filepath);

            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($filepath);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($filepath);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($filepath);
                    break;
                default:
                    return false;
            }

            if (!$image) {
                return false;
            }

            switch ($orientation) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;
                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
            }

            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    imagejpeg($image, $filepath, 85);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($image, $filepath, 8);
                    break;
                case IMAGETYPE_GIF:
                    imagegif($image, $filepath);
                    break;
            }

            imagedestroy($image);
            return true;
        } catch (\Exception $e) {
            Yii::error("Erro ao corrigir orientação: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Redimensiona imagem mantendo proporção
     * @param string $filepath
     * @param int $maxWidth
     * @param int $maxHeight
     * @return bool
     */
    private function redimensionarImagem($filepath, $maxWidth = 640, $maxHeight = 640)
    {
        try {
            $imageType = exif_imagetype($filepath);

            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($filepath);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($filepath);
                    break;
                case IMAGETYPE_GIF:
                    $source = imagecreatefromgif($filepath);
                    break;
                default:
                    return false;
            }

            if (!$source) {
                return false;
            }

            $width = imagesx($source);
            $height = imagesy($source);

            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);

                $thumb = imagecreatetruecolor($newWidth, $newHeight);

                if ($imageType == IMAGETYPE_PNG) {
                    imagealphablending($thumb, false);
                    imagesavealpha($thumb, true);
                }

                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                switch ($imageType) {
                    case IMAGETYPE_JPEG:
                        imagejpeg($thumb, $filepath, 85);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($thumb, $filepath, 8);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($thumb, $filepath);
                        break;
                }

                imagedestroy($thumb);
            }

            imagedestroy($source);
            return true;
        } catch (\Exception $e) {
            Yii::error("Erro ao redimensionar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new Doacao model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->identity->trote_id == 1) {
            return $this->redirect(['users/perfil']);
        }

        $model = new Doacao();
        $model->ativo = 1;
        $this->layout = 'adminsemjquery';
        $erro = $config = array();
        $config["tamanho"] = 2500000;
        $config["largura"] = 640;
        $config["altura"] = 640;
        $arr_extensao = array("image/jpeg", "image/jpg", "image/png", "image/gif");

        if ($model->load(Yii::$app->request->post())) {
            $arquivo = UploadedFile::getInstance($model, 'file');
            $model->user_create = Yii::$app->user->identity->id;
            $model->data_create = date('Y-m-d H:i:s');

            if ($arquivo) {
                if (!in_array($arquivo->type, $arr_extensao)) {
                    $erro[] = "Arquivo em formato inválido! A imagem deve ser jpg, jpeg, gif ou png.";
                }

                if ($arquivo->size > $config["tamanho"]) {
                    $erro[] = "Arquivo muito grande! Máximo " . ($config["tamanho"] / 1000000) . "MB";
                }

                if (empty($erro)) {
                    $mimeTypeMap = [
                        'image/jpeg' => 'jpg',
                        'image/jpg' => 'jpg',
                        'image/png' => 'png',
                        'image/gif' => 'gif',
                    ];

                    $extension = isset($mimeTypeMap[$arquivo->type]) ? $mimeTypeMap[$arquivo->type] : 'jpg';
                    $name = strtotime(date('Y-m-d H:i:s')) . "." . $extension;
                    $path = Yii::$app->basePath . '/web/imagens/doacoes/' . $name;

                    if ($arquivo->saveAs($path)) {
                        $this->corrigirOrientacaoImagem($path);

                        $this->redimensionarImagem($path, $config["largura"], $config["altura"]);

                        $model->arquivo = $name;
                    }
                }
            }

            if (!empty($erro)) {
                $str_erro = implode('<br>', $erro);
                return $this->render('create', [
                    'model' => $model,
                    'error' => true,
                    'success' => false,
                    'msg' => $str_erro
                ]);
            }

            if (!$model->save()) {
                return $this->render('create', [
                    'model' => $model,
                    'error' => true,
                    'success' => false,
                    'msg' => 'Erro ao criar doação'
                ]);
            }

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

    /**
     * Updates an existing Doacao model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->layout = 'adminsemjquery';

        $erro = $config = array();
        $config["tamanho"] = 2500000;
        $config["largura"] = 640;
        $config["altura"] = 640;
        $arr_extensao = array("image/jpeg", "image/jpg", "image/png", "image/gif");

        if ($model->load(Yii::$app->request->post())) {
            $arquivo = UploadedFile::getInstance($model, 'file');
            $model->user_update = Yii::$app->user->identity->id;
            $model->data_update = date('Y-m-d H:i:s');

            if ($arquivo) {
                if (!in_array($arquivo->type, $arr_extensao)) {
                    $erro[] = "Arquivo em formato inválido!";
                }

                if ($arquivo->size > $config["tamanho"]) {
                    $erro[] = "Arquivo muito grande!";
                }

                if (empty($erro)) {
                    $mimeTypeMap = [
                        'image/jpeg' => 'jpg',
                        'image/jpg' => 'jpg',
                        'image/png' => 'png',
                        'image/gif' => 'gif',
                    ];

                    $extension = isset($mimeTypeMap[$arquivo->type]) ? $mimeTypeMap[$arquivo->type] : 'jpg';
                    $name = strtotime(date('Y-m-d H:i:s')) . "." . $extension;
                    $path = Yii::$app->basePath . '/web/imagens/doacoes/' . $name;

                    if ($arquivo->saveAs($path)) {
                        $this->corrigirOrientacaoImagem($path);
                        $this->redimensionarImagem($path, $config["largura"], $config["altura"]);

                        $model->arquivo = $name;
                    }
                }
            }

            if (!empty($erro)) {
                return $this->render('update', [
                    'model' => $model,
                    'error' => true,
                    'success' => false,
                    'msg' => implode('<br>', $erro)
                ]);
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

    /**
     * Deletes an existing Doacao model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->ativo = ($model->ativo == 1) ? 0 : 1;

        if (!$model->save()) {
            Helper::d($model->getErrors());
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Doacao model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Doacao the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Doacao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
