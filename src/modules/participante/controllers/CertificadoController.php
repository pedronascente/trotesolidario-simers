<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\Certificado;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class CertificadoController extends Controller
{
    private CertificadoServiceInterface $certificadoService;

    public function __construct($id, $module, CertificadoServiceInterface $certificadoService, $config = [])
    {
        $this->certificadoService = $certificadoService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'imprime'],
                'rules' => [
                    [
                        'actions' => ['index', 'imprime'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/auth/login']);
        }

        if (!Yii::$app->user->identity->isParticipante()) {
            throw new ForbiddenHttpException('Acesso negado');
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $this->layout = 'adminindex';

        $certificados = Certificado::find()
            ->with(['participacao.trote', 'participacao.universidade'])
            ->joinWith('participacao')
            ->where(['participacao.user_id' => Yii::$app->user->id])
            ->orderBy(['data_emissao' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'certificados' => $certificados,
        ]);
    }

    public function actionImprime($id)
    {
        $model = $this->certificadoService->ensurePdf($this->findModel((int) $id), true);

        $path = $model->getArquivoPdfPath();
        if ($path !== null) {
            return Yii::$app->response->sendFile($path, basename($path), ['inline' => true]);
        }

        return $this->render('@app/modules/common/views/certificado/template', [
            'model' => $model,
            'renderMode' => 'web',
        ]);
    }

    protected function findModel(int $id): Certificado
    {
        $model = Certificado::find()
            ->with(['participacao.user', 'participacao.trote', 'participacao.universidade', 'emissor'])
            ->joinWith('participacao')
            ->where([
                'certificado.id' => $id,
                'participacao.user_id' => Yii::$app->user->id,
            ])
            ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Certificado nao encontrado.');
    }
}
