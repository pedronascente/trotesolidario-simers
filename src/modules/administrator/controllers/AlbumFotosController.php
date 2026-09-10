<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\AlbumFoto;
use app\modules\common\models\AlbumFotoSearchModel;
use app\modules\common\models\Participacao;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class AlbumFotosController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [[
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => static fn() => Yii::$app->user->identity->isAdmin(),
                ]],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }
                    throw new ForbiddenHttpException('Acesso negado.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->layout = 'adminsemjquery';
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new AlbumFotoSearchModel();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel((int) $id)]);
    }

    public function actionCreate()
    {
        $model = new AlbumFoto();
        if ($this->saveModel($model)) {
            Yii::$app->session->setFlash('success', 'Foto adicionada ao álbum com sucesso.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model, 'participacoes' => $this->getParticipacoes()]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel((int) $id);
        if ($this->saveModel($model)) {
            Yii::$app->session->setFlash('success', 'Foto atualizada com sucesso.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model, 'participacoes' => $this->getParticipacoes()]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel((int) $id);
        $path = $model->getImagemPath();
        if ($model->delete() !== false) {
            if (is_file($path)) {
                @unlink($path);
            }
            Yii::$app->session->setFlash('success', 'Foto excluída com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível excluir a foto.');
        }
        return $this->redirect(['index']);
    }

    public function actionArquivo($id)
    {
        $model = $this->findModel((int) $id);
        $path = $model->getImagemPath();
        if (!is_file($path)) {
            throw new NotFoundHttpException('Arquivo da foto não encontrado.');
        }
        return Yii::$app->response->sendFile($path, $model->imagem, ['inline' => true]);
    }

    private function saveModel(AlbumFoto $model): bool
    {
        if (!Yii::$app->request->isPost || !$model->load(Yii::$app->request->post())) {
            return false;
        }

        $oldImage = $model->imagem;
        $model->arquivoImagem = UploadedFile::getInstance($model, 'arquivoImagem');
        if (!$model->validate()) {
            return false;
        }

        $newPath = null;
        if ($model->arquivoImagem !== null) {
            $directory = Yii::getAlias('@app/web/imagens/album-fotos');
            if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                $model->addError('arquivoImagem', 'Não foi possível preparar o diretório de imagens.');
                return false;
            }
            $model->imagem = Yii::$app->security->generateRandomString(32) . '.' . strtolower($model->arquivoImagem->extension);
            $newPath = $directory . DIRECTORY_SEPARATOR . $model->imagem;
            if (!$model->arquivoImagem->saveAs($newPath)) {
                $model->imagem = $oldImage;
                $model->addError('arquivoImagem', 'Não foi possível salvar a imagem.');
                return false;
            }
        }

        if (!$model->save(false)) {
            if ($newPath !== null && is_file($newPath)) {
                @unlink($newPath);
            }
            $model->imagem = $oldImage;
            return false;
        }

        if ($newPath !== null && $oldImage && $oldImage !== $model->imagem) {
            $oldPath = Yii::getAlias('@app/web/imagens/album-fotos') . DIRECTORY_SEPARATOR . $oldImage;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }
        return true;
    }

    private function getParticipacoes(): array
    {
        $models = Participacao::find()->with(['user', 'trote', 'universidade'])->orderBy(['id' => SORT_DESC])->all();
        return ArrayHelper::map($models, 'id', static fn(Participacao $model) => $model->getDisplayLabel());
    }

    private function findModel(int $id): AlbumFoto
    {
        $model = AlbumFoto::find()->with(['participacao.user', 'participacao.trote', 'participacao.universidade'])->where(['album_foto.id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Foto não encontrada.');
        }
        return $model;
    }
}
