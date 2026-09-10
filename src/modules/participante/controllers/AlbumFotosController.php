<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\AlbumFoto;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class AlbumFotosController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'arquivo'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action): bool
    {
        if (Yii::$app->user->isGuest) {
            throw new UnauthorizedHttpException('Efetue login para continuar.');
        }

        if (!Yii::$app->user->identity->isParticipante()) {
            throw new ForbiddenHttpException('Acesso negado.');
        }

        return parent::beforeAction($action);
    }

    public function actionIndex(): string
    {
        $this->layout = 'adminindex';
        $fotos = [];

        $albumFotos = AlbumFoto::find()
            ->alias('af')
            ->innerJoinWith(['participacao p', 'participacao.trote t'])
            ->where(['p.user_id' => Yii::$app->user->id])
            ->orderBy(['af.created_at' => SORT_DESC, 'af.id' => SORT_DESC])
            ->all();

        foreach ($albumFotos as $foto) {
            if ($this->getImagePath($foto) === null) {
                continue;
            }

            $fotos[] = [
                'id' => $foto->id,
                'titulo' => $foto->titulo,
            ];
        }

        return $this->render('index', [
            'fotos' => $fotos,
        ]);
    }

    public function actionArquivo(int $id, bool $download = false)
    {
        $foto = AlbumFoto::find()
            ->alias('af')
            ->innerJoinWith('participacao p')
            ->where([
                'af.id' => $id,
                'p.user_id' => Yii::$app->user->id,
            ])
            ->one();

        if ($foto === null || ($path = $this->getImagePath($foto)) === null) {
            throw new NotFoundHttpException('Foto não encontrada.');
        }

        return Yii::$app->response->sendFile($path, basename($foto->imagem), [
            'inline' => !$download,
        ]);
    }

    private function getImagePath(AlbumFoto $foto): ?string
    {
        if (empty($foto->imagem) || basename($foto->imagem) !== $foto->imagem) {
            return null;
        }

        $path = Yii::getAlias('@app/web/imagens/album-fotos') . DIRECTORY_SEPARATOR . $foto->imagem;

        return is_file($path) && @getimagesize($path) !== false ? $path : null;
    }
}
