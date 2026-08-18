<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\Participacao;
use app\modules\common\models\ParticipacaoSearchModel;
use app\modules\common\models\ParticipacaoUniversidadeChangeRequest;
use app\modules\common\models\UniversityCorrectionReviewForm;
use app\modules\common\services\contracts\ParticipacaoServiceInterface;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ParticipacaoController extends Controller
{
    private ParticipacaoServiceInterface $service;

    public function __construct($id, $module, ParticipacaoServiceInterface $service, $config = [])
    {
        $this->service = $service;
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
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }

                    throw new ForbiddenHttpException('Acesso negado');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
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
        $searchModel = new ParticipacaoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionCreate()
    {
        $model = new Participacao();
        $users = $this->service->findUsers();
        $trotes = $this->service->findTrotes();
        $universidades = $this->service->findUniversidades();

        if ($model->load(Yii::$app->request->post()) && $this->service->create($model)) {
            Yii::$app->session->setFlash('success', 'Participacao criada com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('create', compact('model', 'users', 'trotes', 'universidades'));
    }

    public function actionUpdate($id)
    {
        $model = $this->service->findModel((int) $id);
        if (!$model) {
            throw new NotFoundHttpException('Participacao nao encontrada.');
        }

        $users = $this->service->findUsers();
        $trotes = $this->service->findTrotes();
        $universidades = $this->service->findUniversidades();

        if ($model->load(Yii::$app->request->post()) && $this->service->update($model)) {
            Yii::$app->session->setFlash('success', 'Participacao atualizada com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('update', compact('model', 'users', 'trotes', 'universidades'));
    }

    public function actionDelete($id)
    {
        $model = $this->service->findModel((int) $id);
        if (!$model) {
            throw new NotFoundHttpException('Participacao nao encontrada.');
        }

        try {
            $this->service->delete($model);
            Yii::$app->session->setFlash('success', 'Participacao excluida com sucesso.');
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    public function actionSolicitacoesCorrecaoUniversidade()
    {
        $pendentes = $this->service->findUniversityCorrectionRequests(ParticipacaoUniversidadeChangeRequest::STATUS_PENDENTE);
        $historico = array_values(array_filter(
            $this->service->findUniversityCorrectionRequests(null, 200),
            static fn($request) => $request->status !== ParticipacaoUniversidadeChangeRequest::STATUS_PENDENTE
        ));

        return $this->render('solicitacoes-correcao-universidade', [
            'pendentes' => $pendentes,
            'historico' => $historico,
        ]);
    }

    public function actionAnalisarCorrecaoUniversidade($id)
    {
        $request = $this->service->findUniversityCorrectionRequest((int) $id);
        if ($request === null) {
            throw new NotFoundHttpException('Solicitacao nao encontrada.');
        }

        if ($request->status !== ParticipacaoUniversidadeChangeRequest::STATUS_PENDENTE) {
            Yii::$app->session->setFlash('error', 'Esta solicitacao ja foi analisada.');
            return $this->redirect(['solicitacoes-correcao-universidade']);
        }

        $model = new UniversityCorrectionReviewForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                if ($model->decision === UniversityCorrectionReviewForm::DECISION_APPROVE) {
                    $this->service->approveUniversityCorrectionRequest((int) $request->id, (int) Yii::$app->user->id, $model->review_notes);
                    Yii::$app->session->setFlash('success', 'Solicitacao aprovada e participacao atualizada com sucesso.');
                } else {
                    $this->service->rejectUniversityCorrectionRequest((int) $request->id, (int) Yii::$app->user->id, $model->review_notes);
                    Yii::$app->session->setFlash('success', 'Solicitacao reprovada com sucesso.');
                }

                return $this->redirect(['solicitacoes-correcao-universidade']);
            } catch (Throwable $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('analisar-correcao-universidade', [
            'requestModel' => $request,
            'model' => $model,
        ]);
    }
}
