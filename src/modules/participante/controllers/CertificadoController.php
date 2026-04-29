<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\Certificado;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use app\modules\common\services\CertificadoRepository;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class CertificadoController extends Controller
{
    private CertificadoServiceInterface $certificadoService;
    private CertificadoRepository $certificadoRepository;

    public function __construct(
        $id,
        $module,
        CertificadoServiceInterface $certificadoService,
        ?CertificadoRepository $certificadoRepository = null,
        $config = []
    ) {
        $this->certificadoService = $certificadoService;
        $this->certificadoRepository = $certificadoRepository ?? new CertificadoRepository();
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

        $certificados = $this->certificadoRepository->findAllByUserId(Yii::$app->user->id);

        return $this->render('index', [
            'certificados' => $certificados,
        ]);
    }

    public function actionImprime($id)
    {
        $model = $this->certificadoRepository->findByIdAndUserId((int) $id, Yii::$app->user->id);
        $model = $this->certificadoService->ensurePdf($model, true);

        $path = $model->getArquivoPdfPath();
        if ($path !== null) {
            return Yii::$app->response->sendFile($path, basename($path), ['inline' => true]);
        }

        return $this->render('@app/modules/common/views/certificado/template', [
            'model' => $model,
            'renderMode' => 'web',
        ]);
    }
}