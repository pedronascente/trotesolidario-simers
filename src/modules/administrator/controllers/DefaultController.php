<?php

namespace app\modules\administrator\controllers;

use app\models\User;
use app\modules\common\models\Doacao;
use app\modules\common\models\Evento;
use app\modules\common\models\Participacao;
use app\modules\common\models\Participante;
use app\modules\common\models\Trote;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

class DefaultController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }

                    throw new \yii\web\ForbiddenHttpException('Acesso negado');
                }
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
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/auth/login']);
        }

        if (!Yii::$app->user->identity->isAdmin()) {
            throw new ForbiddenHttpException('Acesso negado');
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->redirect(['home']);
    }

    public function actionHome()
    {
        $this->layout = 'adminsemjquery';

        $totalUsuarios = (int) User::find()->count();
        $totalParticipantes = (int) Participante::find()->count();
        $totalDoacoes = (int) Doacao::find()->count();
        $totalEventos = (int) Evento::find()->count();

        $doacoesPendentes = (int) Doacao::find()->where(['status' => Doacao::STATUS_PENDENTE])->count();
        $doacoesAprovadas = (int) Doacao::find()->where(['status' => Doacao::STATUS_APROVADA])->count();
        $doacoesRejeitadas = (int) Doacao::find()->where(['status' => Doacao::STATUS_REJEITADA])->count();

        $ultimosUsuarios = User::find()
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(5)
            ->all();

        $ultimasDoacoes = Doacao::find()
            ->with(['participacao.user', 'tipoDoacao'])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(5)
            ->all();

        $trotesAtivos = Trote::find()
            ->where(['status' => Trote::STATUS_ATIVO])
            ->orderBy(['data_inicio' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        $troteAtivo = $trotesAtivos[0] ?? null;

        $troteResumo = null;
        $rankingUniversidades = [];
        $trotesAtivosResumo = [];

        if ($troteAtivo !== null) {
            $eventoVinculado = Evento::find()
                ->where(['trote_id' => $troteAtivo->id])
                ->orderBy(['data_evento' => SORT_ASC, 'id' => SORT_ASC])
                ->one();

            $totalParticipacoesAtivas = (int) Participacao::find()
                ->where([
                    'trote_id' => $troteAtivo->id,
                    'status' => Participacao::STATUS_ATIVO,
                ])
                ->count();

            $troteResumo = [
                'nome' => $troteAtivo->titulo ?: ('Trote ' . $troteAtivo->edicao),
                'edicao' => $troteAtivo->edicao,
                'data_inicio' => $troteAtivo->data_inicio,
                'data_fim' => $troteAtivo->data_fim,
                'evento' => $eventoVinculado?->nome,
                'status' => $troteAtivo->getStatusLabel(),
                'total_participantes' => $totalParticipacoesAtivas,
            ];

            $rankingService = Yii::$container->get(RankingCacheServiceInterface::class);
            $rankingUniversidades = array_slice($rankingService->getUniversityRanking((int) $troteAtivo->id), 0, 5);
        }

        foreach ($trotesAtivos as $trote) {
            $trotesAtivosResumo[] = [
                'id' => (int) $trote->id,
                'nome' => $trote->titulo ?: ('Trote ' . $trote->edicao),
                'edicao' => $trote->edicao,
            ];
        }

        return $this->render('home', [
            'totalUsuarios' => $totalUsuarios,
            'totalParticipantes' => $totalParticipantes,
            'totalDoacoes' => $totalDoacoes,
            'totalEventos' => $totalEventos,
            'doacoesPendentes' => $doacoesPendentes,
            'doacoesAprovadas' => $doacoesAprovadas,
            'doacoesRejeitadas' => $doacoesRejeitadas,
            'ultimosUsuarios' => $ultimosUsuarios,
            'ultimasDoacoes' => $ultimasDoacoes,
            'troteResumo' => $troteResumo,
            'trotesAtivosResumo' => $trotesAtivosResumo,
            'rankingUniversidades' => $rankingUniversidades,
        ]);
    }
}

