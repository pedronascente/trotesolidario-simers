<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\Banner;
use app\modules\common\models\Certificado;
use app\modules\common\models\Doacao;
use app\modules\common\models\Documento;
use app\modules\common\models\Participacao;
use app\modules\common\models\Participante;
use app\modules\common\models\ParticipantStartParticipationForm;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use app\modules\common\services\contracts\ParticipacaoServiceInterface;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'start-participation', 'home', 'ranking', 'logout'],
                'rules' => [
                    [
                        'actions' => ['home', 'ranking', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['start-participation'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'start-participation' => ['post'],
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['home', 'ranking', 'logout'], true)) {
            if (Yii::$app->user->isGuest) {
                Yii::$app->response->redirect(['/auth/login']);
                return false;
            }

            if (!Yii::$app->user->identity->isParticipante()) {
                throw new ForbiddenHttpException('Acesso negado');
            }
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isParticipante()) {
            return $this->redirect(['home']);
        }

        $this->layout = 'index';
        $capa = Banner::find()->where(['ativo' => 1, 'tipo' => Banner::TIPO_LOGIN])->orderBy(['id' => SORT_DESC])->one();
        $universidades = Universidade::find()->where(['ativo' => 1])->orderBy(['nome' => SORT_ASC])->all();


       $debug_array =  [
            'model' => new \app\models\LoginForm(),
            'capa' => $capa,
            'universidades_botoes' => $universidades,
       ] ;

        return $this->render('index', [
            'model' => new \app\models\LoginForm(),
            'capa' => $capa,
            'universidades_botoes' => $universidades,
        ]);
    }

    public function actionStartParticipation()
    {
        $model = $this->createStartParticipationForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->validate()) {
            return $this->buildStartParticipationFailureResponse(
                $model->getFirstError('cpf') ?: 'Informe o CPF para continuar.'
            );
        }

        try {
            $existingUser = $model->findExistingUser();
        } catch (\Throwable $e) {
            Yii::error('Falha ao validar CPF na entrada do participante: ' . $e->getMessage(), __METHOD__);

            return $this->buildStartParticipationFailureResponse(
                'Nao foi possivel validar o CPF neste momento. Tente novamente em instantes.'
            );
        }

        if ($existingUser !== null) {
            return $this->buildStartParticipationSuccessResponse(
                'Ja existe um participante cadastrado com este CPF. Faca login para acessar seu painel e concluir sua participacao.',
                Url::to(['/auth/login', 'cpf' => $model->getFormattedCpf()])
            );
        }

        return $this->buildStartParticipationSuccessResponse(
            'CPF nao encontrado em nossa base. Complete seu cadastro para realizar sua participacao.',
            Url::to(['/participante/register/index', 'cpf' => $model->getFormattedCpf()])
        );
    }

    public function actionHome()
    {
        $this->layout = 'adminindex';

        $userId = (int) Yii::$app->user->id;
        $participacaoService = Yii::$container->get(ParticipacaoServiceInterface::class);
        $participante = Participante::findOne(['user_id' => $userId]);
        $isAcademicParticipant = $participante !== null && (int) $participante->estudante === 1;

        $trotesAtivos = Trote::find()
            ->where(['status' => Trote::STATUS_ATIVO])
            ->orderBy(['data_inicio' => SORT_DESC, 'id' => SORT_DESC])
            ->all();
        $troteAtivoGlobal = !empty($trotesAtivos) ? $trotesAtivos[0] : null;

        $participacaoAtivaNoTroteGlobal = null;
        if ($troteAtivoGlobal !== null) {
            $participacaoAtivaNoTroteGlobal = Participacao::find()
                ->where([
                    'user_id' => $userId,
                    'trote_id' => (int) $troteAtivoGlobal->id,
                    'status' => Participacao::STATUS_ATIVO,
                ])
                ->orderBy(['id' => SORT_DESC])
                ->one();
        }

        $showStartParticipationCard = $isAcademicParticipant
            && $troteAtivoGlobal !== null
            && $participacaoAtivaNoTroteGlobal === null;

        $startParticipationModel = new Participacao();
        $startParticipationUniversidades = $showStartParticipationCard ? $this->findActiveUniversidadesForStartParticipation() : [];
        $shouldOpenStartParticipationModal = false;

        if (
            $showStartParticipationCard
            && Yii::$app->request->isPost
            && Yii::$app->request->post('participation_form') === 'start-active-trote'
        ) {
            $startParticipationModel->load(Yii::$app->request->post());
            $startParticipationModel->user_id = $userId;
            $startParticipationModel->trote_id = (int) $troteAtivoGlobal->id;
            $startParticipationModel->status = Participacao::STATUS_ATIVO;

            try {
                if ($participacaoService->create($startParticipationModel)) {
                    Yii::$app->session->setFlash('success', 'Sua participacao no trote ativo foi iniciada com sucesso.');

                    return $this->redirect(['home', 'trote_id' => (int) $troteAtivoGlobal->id]);
                }
            } catch (\Throwable $e) {
                Yii::error('Falha ao iniciar participacao do academico no trote ativo: ' . $e->getMessage(), __METHOD__);
                Yii::$app->session->setFlash('error', 'Nao foi possivel iniciar sua participacao agora. Tente novamente em instantes.');
            }

            $shouldOpenStartParticipationModal = true;
        }

        $participacoes = Participacao::find()
            ->with(['trote', 'universidade'])
            ->where(['user_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $participacoesAtivas = array_values(array_filter($participacoes, static function (Participacao $participacao) {
            return $participacao->status === Participacao::STATUS_ATIVO;
        }));

        $trotesDisponiveis = [];
        foreach ($participacoesAtivas as $participacao) {
            if ($participacao->trote === null || $participacao->trote->status !== Trote::STATUS_ATIVO) {
                continue;
            }

            $trotesDisponiveis[(int) $participacao->trote_id] = $participacao->trote;
        }

        $selectedTroteId = Yii::$app->request->get('trote_id');
        $selectedTroteId = $selectedTroteId !== null && $selectedTroteId !== '' ? (int) $selectedTroteId : null;
        $troteAtivo = $this->resolveSelectedHomeTrote($trotesDisponiveis, $selectedTroteId);
        $selectedTroteId = $troteAtivo ? (int) $troteAtivo->id : null;

        $doacaoQuery = Doacao::find()
            ->joinWith('participacao')
            ->where(['participacao.user_id' => $userId]);

        if ($selectedTroteId !== null) {
            $doacaoQuery->andWhere(['participacao.trote_id' => $selectedTroteId]);
        }
        $totalDoacoes = (int) (clone $doacaoQuery)->count('doacao.id');
        $totalDoacoesAprovadas = (int) (clone $doacaoQuery)
            ->andWhere(['doacao.status' => Doacao::STATUS_APROVADA])
            ->count('doacao.id');
        $totalDoacoesPendentes = (int) (clone $doacaoQuery)
            ->andWhere(['doacao.status' => Doacao::STATUS_PENDENTE])
            ->count('doacao.id');

        $certificadoQuery = Certificado::find()
            ->joinWith('participacao')
            ->where(['participacao.user_id' => $userId]);

        if ($selectedTroteId !== null) {
            $certificadoQuery->andWhere(['participacao.trote_id' => $selectedTroteId]);
        }
        $totalCertificados = (int) $certificadoQuery->count('certificado.id');

        $ranking = [];
        if ($troteAtivo !== null) {
            $rankingService = Yii::$container->get(RankingCacheServiceInterface::class);
            $ranking = array_slice($rankingService->getUniversityRanking((int) $troteAtivo->id), 0, 5);
        }

        $universidadesDoacao = Universidade::find()
            ->where(['ativo' => 1])
            ->andWhere(['not', ['link_doacao_alimento' => null]])
            ->andWhere(['<>', 'link_doacao_alimento', ''])
            ->orderBy(['nome' => SORT_ASC])
            ->all();

        return $this->render('home', [
            'banner' => Banner::find()->where(['ativo' => 1, 'tipo' => Banner::TIPO_HOME])->orderBy(['id' => SORT_DESC])->one(),
            'informativos' => Documento::find()
                ->where(['tipo' => Documento::TIPO_INFORMATIVO])
                ->andWhere(['not', ['arquivo' => null]])
                ->andWhere(['<>', 'arquivo', ''])
                ->orderBy(['id' => SORT_DESC])
                ->limit(6)
                ->all(),
            'regulamentos' => Documento::find()
                ->where(['tipo' => Documento::TIPO_REGULAMENTO])
                ->andWhere(['not', ['arquivo' => null]])
                ->andWhere(['<>', 'arquivo', ''])
                ->orderBy(['id' => SORT_DESC])
                ->limit(6)
                ->all(),
            'participacoes' => $participacoes,
            'troteAtivo' => $troteAtivo,
            'selectedTroteId' => $selectedTroteId,
            'trotesDisponiveis' => $trotesDisponiveis,
            'dashboardSummary' => [
                'doacoes' => $totalDoacoes,
                'doacoesAprovadas' => $totalDoacoesAprovadas,
                'doacoesPendentes' => $totalDoacoesPendentes,
                'certificados' => $totalCertificados,
            ],
            'ranking' => $ranking,
            'universidadesDoacao' => $universidadesDoacao,
            'showStartParticipationCard' => $showStartParticipationCard,
            'startParticipationModel' => $startParticipationModel,
            'startParticipationUniversidades' => $startParticipationUniversidades,
            'shouldOpenStartParticipationModal' => $shouldOpenStartParticipationModal,
            'troteAtivoGlobal' => $troteAtivoGlobal,
        ]);
    }

    public function actionRanking()
    {
        $this->layout = 'adminindex';

        $service = Yii::$container->get(RankingCacheServiceInterface::class);
        $trotes = $service->findTrotes();

        $participacoesAtivas = Participacao::find()
            ->with(['trote', 'universidade'])
            ->where([
                'user_id' => Yii::$app->user->id,
                'status' => Participacao::STATUS_ATIVO,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $troteAtivo = null;
        foreach ($participacoesAtivas as $participacao) {
            if ($participacao->trote && $participacao->trote->status === Trote::STATUS_ATIVO) {
                $troteAtivo = $participacao->trote;
                break;
            }
        }

        $selectedTroteId = Yii::$app->request->get('trote_id');
        $selectedTroteId = $selectedTroteId !== null && $selectedTroteId !== '' ? (int) $selectedTroteId : ($troteAtivo->id ?? null);

        $ranking = $selectedTroteId !== null ? $service->getUniversityRanking($selectedTroteId) : [];

        $userUniversityIds = [];
        foreach ($participacoesAtivas as $participacao) {
            if ($participacao->universidade_id === null) {
                continue;
            }

            if ($selectedTroteId !== null && (int) $participacao->trote_id !== $selectedTroteId) {
                continue;
            }

            $userUniversityIds[] = (int) $participacao->universidade_id;
        }

        $userUniversityIds = array_values(array_unique($userUniversityIds));

        return $this->render('ranking', [
            'trotes' => $trotes,
            'selectedTroteId' => $selectedTroteId,
            'ranking' => $ranking,
            'troteAtivo' => $troteAtivo,
            'userUniversityIds' => $userUniversityIds,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['/auth/login']);
    }

    private function resolveSelectedHomeTrote(array $trotesDisponiveis, ?int $selectedTroteId): ?Trote
    {
        if ($selectedTroteId !== null && isset($trotesDisponiveis[$selectedTroteId])) {
            return $trotesDisponiveis[$selectedTroteId];
        }

        if (count($trotesDisponiveis) === 1) {
            return array_values($trotesDisponiveis)[0];
        }

        return null;
    }

    protected function createStartParticipationForm(): ParticipantStartParticipationForm
    {
        return new ParticipantStartParticipationForm();
    }

    protected function buildStartParticipationSuccessResponse(string $message, string $redirectUrl)
    {
        Yii::$app->session->setFlash('info', $message);

        if ($this->isAjaxStartParticipationRequest()) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'redirectUrl' => $redirectUrl,
            ];
        }

        return $this->redirect($redirectUrl);
    }

    protected function buildStartParticipationFailureResponse(string $message)
    {
        if ($this->isAjaxStartParticipationRequest()) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => false,
                'message' => $message,
            ];
        }

        Yii::$app->session->setFlash('error', $message);

        return $this->redirect(['index']);
    }

    protected function isAjaxStartParticipationRequest(): bool
    {
        return Yii::$app->request->isAjax;
    }

    private function findActiveUniversidadesForStartParticipation(): array
    {
        $universidades = Universidade::find()
            ->where(['ativo' => 1])
            ->orderBy(['nome' => SORT_ASC])
            ->all();

        return ArrayHelper::map($universidades, 'id', static function (Universidade $universidade) {
            $cidade = $universidade->cidade ?: '-';
            $uf = $universidade->uf ?: '-';

            return $universidade->nome . ' | ' . $cidade . '/' . $uf;
        });
    }
}
