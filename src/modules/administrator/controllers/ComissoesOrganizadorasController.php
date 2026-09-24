<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\ComissaoOrganizadora;
use app\modules\common\models\ComissaoOrganizadoraSearchModel;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ComissoesOrganizadorasController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [[
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function () {
                        return Yii::$app->user->identity->isAdmin();
                    },
                ]],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }

                    throw new ForbiddenHttpException('Acesso negado');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
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
        $searchModel = new ComissaoOrganizadoraSearchModel();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
        ]);
    }

    public function actionCreate()
    {
        $model = new ComissaoOrganizadora();
        if ($this->saveModel($model)) {
            Yii::$app->session->setFlash('success', 'Membro adicionado à comissão organizadora.');
            return $this->redirect(['index']);
        }
        return $this->render('create', ['model' => $model, 'trotes' => $this->getTrotes($model), 'universidades' => $this->getUniversidades()]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel((int) $id);
        if ($this->saveModel($model)) {
            Yii::$app->session->setFlash('success', 'Membro da comissão atualizado.');
            return $this->redirect(['index']);
        }
        return $this->render('update', ['model' => $model, 'trotes' => $this->getTrotes($model), 'universidades' => $this->getUniversidades()]);
    }

    public function actionDelete($id)
    {
        if ($this->findModel((int) $id)->delete() !== false) {
            Yii::$app->session->setFlash('success', 'Membro removido da comissão organizadora.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível remover o membro da comissão.');
        }
        return $this->redirect(['index']);
    }

    private function saveModel(ComissaoOrganizadora $model): bool
    {
        return Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save();
    }

    private function getUniversidades(): array
    {
        return ArrayHelper::map(
            Universidade::find()->where(['ativo' => 1])->orderBy(['nome' => SORT_ASC])->all(),
            'id',
            'nome'
        );
    }

    private function getTrotes(ComissaoOrganizadora $model): array
    {
        $trotes = ArrayHelper::map(
            Trote::getAtivos(),
            'id',
            static fn(Trote $trote) => $trote->titulo . ' — ' . $trote->edicao
        );

        if (!$model->isNewRecord && $model->trote !== null) {
            $trotes[$model->trote->id] = $model->trote->titulo . ' — ' . $model->trote->edicao;
        }

        return $trotes;
    }

    private function findModel(int $id): ComissaoOrganizadora
    {
        $model = ComissaoOrganizadora::find()
            ->with(['trote', 'universidade'])
            ->where(['comissao_organizadora.id' => $id])
            ->one();
        if ($model === null) {
            throw new NotFoundHttpException('Membro da comissão não encontrado.');
        }
        return $model;
    }
}
