<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\Banner;
use app\modules\common\models\Certificado;
use app\modules\common\models\Doacao;
use app\modules\common\models\Documento;
use app\modules\common\models\Participacao;
use app\modules\common\models\Regulamento;
use app\modules\common\models\Trote;
use app\modules\common\services\RankingCacheService;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class DefaultController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'home', 'ranking', 'logout'],
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
        if (in_array($action->id, ['home', 'ranking', 'logout'], true)) {
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/auth/login']);
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

        $this->layout = 'login';
        $capa = Banner::find()->where(['ativo' => 1, 'tipo' => Banner::TIPO_LOGIN])->one();
        $universidades = \app\modules\common\models\Universidade::find()->where(['ativo' => 1])->orderBy(['nome' => SORT_ASC])->all();

        return $this->render('index', [
            'model' => new \app\models\LoginForm(),
            'capa' => $capa,
            'universidades_botoes' => $universidades,
        ]);
    }

    public function actionHome()
    {
        $this->layout = 'adminindex';

        $userId = Yii::$app->user->id;

        $participacoes = Participacao::find()
            ->with(['trote', 'universidade'])
            ->where(['user_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $participacoesAtivas = array_values(array_filter($participacoes, static function (Participacao $participacao) {
            return $participacao->status === Participacao::STATUS_ATIVO;
        }));

        $troteAtivo = null;
        foreach ($participacoesAtivas as $participacao) {
            if ($participacao->trote && $participacao->trote->status === Trote::STATUS_ATIVO) {
                $troteAtivo = $participacao->trote;
                break;
            }
        }

        $doacoes = Doacao::find()
            ->with(['tipoDoacao', 'participacao.trote', 'participacao.universidade'])
            ->joinWith('participacao')
            ->where(['participacao.user_id' => $userId])
            ->orderBy(['doacao.created_at' => SORT_DESC, 'doacao.id' => SORT_DESC])
            ->all();

        $certificados = Certificado::find()
            ->joinWith('participacao')
            ->where(['participacao.user_id' => $userId])
            ->orderBy(['data_emissao' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        $ranking = [];
        if ($troteAtivo !== null) {
            $ranking = (new Query())
                ->select([
                    'u.nome',
                    'SUM(rc.pontuacao_total) AS pontos',
                    'COUNT(DISTINCT rc.participacao_id) AS participantes',
                ])
                ->from(['rc' => 'ranking_cache'])
                ->innerJoin(['p' => 'participacao'], 'p.id = rc.participacao_id')
                ->innerJoin(['u' => 'universidade'], 'u.id = p.universidade_id')
                ->where(['rc.trote_id' => $troteAtivo->id])
                ->groupBy(['u.id', 'u.nome'])
                ->orderBy(['pontos' => SORT_DESC, 'u.nome' => SORT_ASC])
                ->limit(5)
                ->all();
        }

        return $this->render('home', [
            'banner' => Banner::find()->where(['ativo' => 1, 'tipo' => Banner::TIPO_HOME])->one(),
            'informativos' => Documento::find()->orderBy(['id' => SORT_DESC])->all(),
            'regulamentos' => Documento::find()->where(['tipo' => Documento::TIPO_REGULAMENTO])->orderBy(['id' => SORT_DESC])->all(),
            'participacoes' => $participacoes,
            'participacoesAtivas' => $participacoesAtivas,
            'troteAtivo' => $troteAtivo,
            'doacoes' => $doacoes,
            'certificados' => $certificados,
            'ranking' => $ranking,
        ]);
    }

    public function actionRanking()
    {
        $this->layout = 'adminindex';

        $service = new RankingCacheService();
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

        $ranking = $service->getUniversityRanking($selectedTroteId);

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
}

