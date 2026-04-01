<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\Doacao;
use app\modules\common\models\DoacaoSearchModel;
use app\modules\common\models\Participacao;
use app\modules\common\services\contracts\DoacaoServiceInterface;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

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
                'only' => ['index', 'create', 'update', 'delete', 'view'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
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
            if (!$this->pertenceAoUsuarioLogado((int) $model->participacao_id)) {
                throw new ForbiddenHttpException('Participacao invalida para este usuario.');
            }

            if ($this->service->create($model)) {
                Yii::$app->session->setFlash('success', 'Doacao criada com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao criar doacao');
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

        if ($model->load(Yii::$app->request->post())) {
            if (!$this->pertenceAoUsuarioLogado((int) $model->participacao_id)) {
                throw new ForbiddenHttpException('Participacao invalida para este usuario.');
            }

            if ($this->service->update($model)) {
                Yii::$app->session->setFlash('success', 'Doacao atualizada com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao atualizar doacao');
        }

        return $this->render('update', array_merge(['model' => $model], $data));
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->status === Doacao::STATUS_APROVADA) {
            throw new ForbiddenHttpException('Doacoes aprovadas nao podem ser excluidas.');
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'Doacao excluida com sucesso');

        return $this->redirect(['index']);
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

    private function pertenceAoUsuarioLogado(int $participacaoId): bool
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
}