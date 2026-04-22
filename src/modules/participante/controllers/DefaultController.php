<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\Banner;
use app\modules\common\models\Certificado;
use app\modules\common\models\Doacao;
use app\modules\common\models\Documento;
use app\modules\common\models\Participacao;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class DefaultController extends Controller
{
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
                'actions' => [
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

        $this->layout = 'login';
        $capa = Banner::find()->where(['ativo' => 1, 'tipo' => Banner::TIPO_LOGIN])->one();
        $universidades = Universidade::find()->where(['ativo' => 1])->orderBy(['nome' => SORT_ASC])->all();


       $debug_array =  [
            'model' => new \app\models\LoginForm(),
            'capa' => $capa,
            'universidades_botoes' => $universidades,
       ] ; 


       //echo '<pre>'; print_r( $debug_array);die;


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
            ->with(['tipoDoacao', 'participacao.trote', 'participacao.universidade'])
            ->joinWith('participacao')
            ->where(['participacao.user_id' => $userId])
            ->orderBy(['doacao.created_at' => SORT_DESC, 'doacao.id' => SORT_DESC]);

        if ($selectedTroteId !== null) {
            $doacaoQuery->andWhere(['participacao.trote_id' => $selectedTroteId]);
        }
        $doacoes = $doacaoQuery->all();

        $certificadoQuery = Certificado::find()
            ->joinWith('participacao')
            ->where(['participacao.user_id' => $userId])
            ->orderBy(['data_emissao' => SORT_DESC, 'id' => SORT_DESC]);

        if ($selectedTroteId !== null) {
            $certificadoQuery->andWhere(['participacao.trote_id' => $selectedTroteId]);
        }
        $certificados = $certificadoQuery->all();

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
            'banner' => Banner::find()->where(['ativo' => 1, 'tipo' => Banner::TIPO_HOME])->one(),
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
            'participacoesAtivas' => $participacoesAtivas,
            'troteAtivo' => $troteAtivo,
            'selectedTroteId' => $selectedTroteId,
            'trotesDisponiveis' => $trotesDisponiveis,
            'doacoes' => $doacoes,
            'certificados' => $certificados,
            'ranking' => $ranking,
            'universidadesDoacao' => $universidadesDoacao,
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
}
