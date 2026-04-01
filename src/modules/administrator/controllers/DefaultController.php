<?php

namespace app\modules\administrator\controllers;

use app\models\User;
use app\modules\common\models\Doacao;
use app\modules\common\models\Evento;
use app\modules\common\models\Participacao;
use app\modules\common\models\Participante;
use app\modules\common\models\Trote;
use Yii;
use yii\db\Query;
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

        $troteAtivo = Trote::find()
            ->where(['status' => Trote::STATUS_ATIVO])
            ->orderBy(['data_inicio' => SORT_DESC, 'id' => SORT_DESC])
            ->one();

        $troteResumo = null;
        $rankingUniversidades = [];

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

            $rankingSchema = Yii::$app->db->schema->getTableSchema('ranking_cache', true);

            if ($rankingSchema !== null) {
                $rankingUniversidades = (new Query())
                    ->select([
                        'u.nome',
                        'rc.pontuacao_total AS pontos',
                        'COUNT(DISTINCT p.id) AS participantes',
                    ])
                    ->from(['rc' => 'ranking_cache'])
                    ->innerJoin(['p' => 'participacao'], 'p.id = rc.participacao_id')
                    ->innerJoin(['u' => 'universidade'], 'u.id = p.universidade_id')
                    ->where(['rc.trote_id' => $troteAtivo->id])
                    ->groupBy(['u.id', 'u.nome', 'rc.pontuacao_total', 'rc.posicao'])
                    ->orderBy(['rc.posicao' => SORT_ASC, 'rc.pontuacao_total' => SORT_DESC, 'u.nome' => SORT_ASC])
                    ->limit(5)
                    ->all();
            }

            if (empty($rankingUniversidades)) {
                $rankingUniversidades = (new Query())
                    ->select([
                        'u.nome',
                        'COUNT(p.id) AS pontos',
                        'COUNT(p.id) AS participantes',
                    ])
                    ->from(['p' => 'participacao'])
                    ->innerJoin(['u' => 'universidade'], 'u.id = p.universidade_id')
                    ->where([
                        'p.trote_id' => $troteAtivo->id,
                        'p.status' => Participacao::STATUS_ATIVO,
                    ])
                    ->groupBy(['u.id', 'u.nome'])
                    ->orderBy(['pontos' => SORT_DESC, 'u.nome' => SORT_ASC])
                    ->limit(5)
                    ->all();
            }
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
            'rankingUniversidades' => $rankingUniversidades,
        ]);
    }
}
