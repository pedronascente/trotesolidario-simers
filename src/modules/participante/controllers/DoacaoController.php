<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\Doacao;
use app\modules\common\models\DoacaoSearchModel;
use app\modules\common\models\Evento;
use app\modules\common\models\Participacao;
use app\modules\common\models\TipoDoacao;
use app\modules\common\services\contracts\DoacaoServiceInterface;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class DoacaoController extends Controller
{
    private DoacaoServiceInterface $service;

    public function __construct($id, $module, DoacaoServiceInterface $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete', 'view', 'eventos-by-participacao'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view', 'eventos-by-participacao'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'eventos-by-participacao' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            throw new UnauthorizedHttpException('Efetue login para continuar.');
        }

        if (!Yii::$app->user->identity->isParticipante()) {
            throw new ForbiddenHttpException('Acesso negado.');
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $this->layout = 'adminindex';

        $searchModel = new DoacaoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['participacao.user_id' => Yii::$app->user->id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterData' => $this->getParticipantFilterData(),
        ]);
    }

    public function actionView($id)
    {
        $this->layout = 'adminindex';

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $this->layout = 'adminindex';

        $model = new Doacao();
        $data = $this->getParticipantFormData();

        if ($model->load(Yii::$app->request->post())) {
            if (!$this->pertenceParticipacaoAtivaAoUsuarioLogado((int) $model->participacao_id)) {
                throw new ForbiddenHttpException('Participacao invalida para este usuario.');
            }

            if ($this->service->create($model)) {
                Yii::$app->session->setFlash('success', 'Doacao criada com sucesso');
                return $this->redirect(['index']);
            }

            if (!$model->hasErrors()) {
                Yii::$app->session->setFlash('error', $this->getModelErrorMessage($model, 'Erro ao criar doacao.'));
            }
        }

        return $this->render('create', array_merge(['model' => $model], $data));
    }

    public function actionUpdate($id)
    {
        $this->layout = 'adminindex';

        $model = $this->findModel($id);
        if (!in_array($model->status, [Doacao::STATUS_PENDENTE, Doacao::STATUS_REJEITADA], true)) {
            throw new ForbiddenHttpException('Somente doacoes pendentes ou rejeitadas podem ser editadas.');
        }

        $data = $this->getParticipantFormData();

        if (!isset($data['participacoes'][$model->participacao_id])) {
            $participacaoAtual = Participacao::find()
                ->with(['user', 'trote', 'universidade'])
                ->where(['id' => $model->participacao_id])
                ->one();

            if ($participacaoAtual !== null) {
                $data['participacoes'][$participacaoAtual->id] = $participacaoAtual->getDisplayLabel();
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->participacao_id = (int) ($model->getOldAttribute('participacao_id') ?? $model->participacao_id);

            if (!$this->pertenceParticipacaoAoUsuarioLogado((int) $model->participacao_id)) {
                throw new ForbiddenHttpException('Participacao invalida para este usuario.');
            }

            if ($this->service->update($model)) {
                Yii::$app->session->setFlash('success', 'Doacao atualizada com sucesso');
                return $this->redirect(['index']);
            }

            if (!$model->hasErrors()) {
                Yii::$app->session->setFlash('error', $this->getModelErrorMessage($model, 'Erro ao atualizar doacao.'));
            }
        }

        return $this->render('update', array_merge(['model' => $model], $data));
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($this->service->delete($model)) {
            Yii::$app->session->setFlash('success', 'Doacao excluida com sucesso');
        } else {
            Yii::$app->session->setFlash('error', $this->getModelErrorMessage($model, 'Erro ao excluir doacao.'));
        }

        return $this->redirect(['index']);
    }

    public function actionEventosByParticipacao(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];

        if ($parents = Yii::$app->request->post('depdrop_parents')) {
            $participacaoId = (int) ($parents[0] ?? 0);
            if ($participacaoId > 0 && $this->pertenceParticipacaoAtivaAoUsuarioLogado($participacaoId)) {
                $eventos = $this->service->getEventosByParticipacao($participacaoId);
                foreach ($eventos as $evento) {
                    $out[] = [
                        'id' => $evento['id'],
                        'name' => $evento['nome'],
                    ];
                }
            }
        }

        return ['output' => $out, 'selected' => ''];
    }

    private function getParticipantFormData(): array
    {
        $data = $this->service->getFormData();
        $participacoesUsuario = Participacao::find()
            ->with(['user', 'trote', 'universidade'])
            ->where([
                'user_id' => Yii::$app->user->id,
                'status' => Participacao::STATUS_ATIVO,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $participacoes = [];
        foreach ($participacoesUsuario as $participacao) {
            $participacoes[$participacao->id] = $participacao->getDisplayLabel();
        }

        $data['participacoes'] = $participacoes;
        return $data;
    }

    private function getParticipantFilterData(): array
    {
        $participacoes = Participacao::find()
            ->with(['user', 'trote', 'universidade'])
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $participacaoMap = ArrayHelper::map($participacoes, 'id', function (Participacao $participacao) {
            return $participacao->getDisplayLabel();
        });

        $troteIds = array_values(array_unique(array_filter(array_map(static function (Participacao $participacao) {
            return $participacao->trote_id !== null ? (int) $participacao->trote_id : null;
        }, $participacoes))));

        $eventoQuery = Evento::find()->orderBy(['nome' => SORT_ASC]);
        if (!empty($troteIds)) {
            $eventoQuery->where(['trote_id' => $troteIds]);
        } else {
            $eventoQuery->where('1=0');
        }

        return [
            'participacoes' => $participacaoMap,
            'tiposDoacao' => ArrayHelper::map(
                TipoDoacao::find()->where(['ativo' => 1])->orderBy(['nome' => SORT_ASC])->all(),
                'id',
                'nome'
            ),
            'eventos' => ArrayHelper::map($eventoQuery->all(), 'id', 'nome'),
        ];
    }

    private function pertenceParticipacaoAtivaAoUsuarioLogado(int $participacaoId): bool
    {
        return Participacao::find()
            ->where([
                'id' => $participacaoId,
                'user_id' => Yii::$app->user->id,
                'status' => Participacao::STATUS_ATIVO,
            ])
            ->exists();
    }

    private function pertenceParticipacaoAoUsuarioLogado(int $participacaoId): bool
    {
        return Participacao::find()
            ->where([
                'id' => $participacaoId,
                'user_id' => Yii::$app->user->id,
            ])
            ->exists();
    }

    protected function findModel($id): Doacao
    {
        $model = Doacao::find()
            ->joinWith('participacao')
            ->where([
                'doacao.id' => $id,
                'participacao.user_id' => Yii::$app->user->id,
            ])
            ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Doacao nao encontrada.');
    }

    private function getModelErrorMessage(Doacao $model, string $fallback): string
    {
        $errors = $model->getFirstErrors();
        return !empty($errors) ? implode(' ', $errors) : $fallback;
    }
}
