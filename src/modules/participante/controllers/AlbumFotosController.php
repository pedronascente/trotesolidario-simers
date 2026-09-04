<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\Doacao;
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

        $doacoes = Doacao::find()
            ->innerJoinWith(['participacao', 'tipoDoacao'])
            ->where(['participacao.user_id' => Yii::$app->user->id])
            ->andWhere(['not', ['doacao.arquivo' => null]])
            ->andWhere(['<>', 'doacao.arquivo', ''])
            ->orderBy(['doacao.created_at' => SORT_DESC])
            ->all();

        foreach ($doacoes as $doacao) {
            if ($this->getImagePath($doacao) === null) {
                continue;
            }

            $fotos[] = [
                'id' => $doacao->id,
                'titulo' => $doacao->tipoDoacao->nome ?? 'Foto da doação #' . $doacao->id,
            ];
        }

        return $this->render('index', [
            'fotos' => $fotos,
        ]);
    }

    public function actionArquivo(int $id, bool $download = false)
    {
        $doacao = Doacao::find()
            ->innerJoinWith('participacao')
            ->where([
                'doacao.id' => $id,
                'participacao.user_id' => Yii::$app->user->id,
            ])
            ->one();

        if ($doacao === null || ($path = $this->getImagePath($doacao)) === null) {
            throw new NotFoundHttpException('Foto não encontrada.');
        }

        return Yii::$app->response->sendFile($path, basename($doacao->arquivo), [
            'inline' => !$download,
        ]);
    }

    private function getImagePath(Doacao $doacao): ?string
    {
        if (empty($doacao->arquivo) || basename($doacao->arquivo) !== $doacao->arquivo) {
            return null;
        }

        $path = Yii::getAlias('@imgArquivosDoacao') . DIRECTORY_SEPARATOR . $doacao->arquivo;

        return is_file($path) && @getimagesize($path) !== false ? $path : null;
    }
}
