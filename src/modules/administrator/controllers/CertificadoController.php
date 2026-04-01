<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\Certificado;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
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
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => static function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => static function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }

                    throw new ForbiddenHttpException('Acesso negado.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'send-email' => ['POST'],
                ],
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
        $query = Certificado::find()
            ->with([
                'participacao.user',
                'participacao.trote.eventos',
                'participacao.universidade',
                'emissor',
            ])
            ->joinWith(['participacao.user', 'participacao.trote'])
            ->orderBy(['certificado.data_emissao' => SORT_DESC, 'certificado.id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['data_emissao' => SORT_DESC, 'id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'codigo_validador',
                    'carga_horaria_total',
                    'data_emissao',
                    'participante_nome' => [
                        'asc' => ['user.nome' => SORT_ASC],
                        'desc' => ['user.nome' => SORT_DESC],
                    ],
                    'participante_email' => [
                        'asc' => ['user.email' => SORT_ASC],
                        'desc' => ['user.email' => SORT_DESC],
                    ],
                    'trote_edicao' => [
                        'asc' => ['trote.edicao' => SORT_ASC],
                        'desc' => ['trote.edicao' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->certificadoService->ensurePdf($this->findModel((int) $id), true);

        $pdfPath = $model->getArquivoPdfPath();
        if ($pdfPath !== null) {
            return Yii::$app->response->sendFile($pdfPath, basename($pdfPath), ['inline' => true]);
        }

        return $this->render('@app/modules/common/views/certificado/template', [
            'model' => $model,
        ]);
    }

    public function actionSendEmail($id)
    {
        $model = $this->certificadoService->ensurePdf($this->findModel((int) $id), true);

        try {
            $this->sendCertificateEmail($model);
            Yii::$app->session->setFlash('success', 'Certificado encaminhado por e-mail com sucesso para ' . $model->participanteEmail . '.');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Certificado
    {
        $model = Certificado::find()
            ->with([
                'participacao.user',
                'participacao.trote.eventos',
                'participacao.universidade',
                'emissor',
            ])
            ->where(['certificado.id' => $id])
            ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Certificado nao encontrado.');
    }

    private function sendCertificateEmail(Certificado $model): void
    {
        $destinatario = $model->participacao->user->email ?? null;
        if (empty($destinatario)) {
            throw new \RuntimeException('O participante deste certificado nao possui e-mail cadastrado.');
        }

        $subject = 'Seu certificado do Trote Solidario - ' . ($model->participacao->trote->edicao ?? '');
        $body = $this->buildEmailBody($model);
        $from = Yii::$app->params['senderEmail'] ?? Yii::$app->params['adminEmail'] ?? 'noreply@example.com';

        $message = Yii::$app->mailer->compose('layouts/html', ['content' => $body])
            ->setFrom($from)
            ->setTo($destinatario)
            ->setSubject($subject);

        if (!empty(Yii::$app->params['adminEmail'])) {
            $message->setBcc(Yii::$app->params['adminEmail']);
        }

        $pdfPath = $model->getArquivoPdfPath();
        if ($pdfPath !== null) {
            $message->attach($pdfPath, ['fileName' => basename($pdfPath)]);
        }

        if (!$message->send()) {
            throw new \RuntimeException('Nao foi possivel encaminhar o certificado por e-mail.');
        }
    }

    private function buildEmailBody(Certificado $model): string
    {
        $participante = Html::encode($model->participanteNome);
        $trote = Html::encode($model->troteDescricao);
        $eventos = Html::encode($model->eventoNomes);
        $codigo = Html::encode($model->codigo_validador);
        $dataEmissao = $model->data_emissao ? date('d/m/Y H:i', strtotime($model->data_emissao)) : '-';
        $cargaHoraria = (int) $model->carga_horaria_total;

        $html = '<p>Ola, <strong>' . $participante . '</strong>.</p>';
        $html .= '<p>Seu certificado do Trote Solidario foi encaminhado por este e-mail.</p>';
        $html .= '<p><strong>Trote:</strong> ' . $trote . '<br>';
        $html .= '<strong>Evento(s):</strong> ' . $eventos . '<br>';
        $html .= '<strong>Carga horaria:</strong> ' . $cargaHoraria . ' hora(s)<br>';
        $html .= '<strong>Data de emissao:</strong> ' . $dataEmissao . '<br>';
        $html .= '<strong>Codigo validador:</strong> ' . $codigo . '</p>';
        $html .= '<p>O arquivo PDF do certificado segue anexado.</p>';
        $html .= '<p>Atenciosamente,<br>Equipe Trote Solidario</p>';

        return $html;
    }
}
