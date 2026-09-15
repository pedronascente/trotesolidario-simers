<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\CategoriaCusto;
use app\modules\common\services\contracts\GestaoCustosServiceInterface;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class GestaoCustosController extends Controller
{
    private GestaoCustosServiceInterface $service;

    public function __construct($id, $module, GestaoCustosServiceInterface $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [[
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => static fn() => Yii::$app->user->identity->isAdmin(),
                ]],
                'denyCallback' => static function () {
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

    public function actionIndex($trote_id = null)
    {
        $troteId = $trote_id !== null && $trote_id !== '' ? (int) $trote_id : null;
        $dashboard = $this->service->getDashboard($troteId);
        return $this->render('index', array_merge($dashboard, [
            'trotes' => $this->service->getTrotes(),
            'troteId' => $troteId,
        ]));
    }

    public function actionCreate()
    {
        return $this->saveForm(new CategoriaCusto(), 'Novo custo');
    }

    public function actionUpdate($id)
    {
        return $this->saveForm($this->findModel((int) $id), 'Editar custo');
    }

    public function actionDelete($id)
    {
        $model = $this->findModel((int) $id);
        try {
            $this->service->delete($model);
            Yii::$app->session->setFlash('success', 'Custo excluido com sucesso.');
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
            Yii::$app->session->setFlash('error', 'Nao foi possivel excluir o custo.');
        }
        return $this->redirect(['index', 'trote_id' => $model->trote_id]);
    }

    private function saveForm(CategoriaCusto $model, string $title)
    {
        $distribuicoes = $model->isNewRecord ? [] : $model->distribuicoes;
        if ($model->load(Yii::$app->request->post())) {
            $dadosDistribuicoes = Yii::$app->request->post('DistribuicaoCusto', []);
            if ($this->service->save($model, $dadosDistribuicoes)) {
                Yii::$app->session->setFlash('success', 'Custo salvo com sucesso.');
                return $this->redirect(['index', 'trote_id' => $model->trote_id]);
            }
            $distribuicoes = $dadosDistribuicoes;
        }

        $universidadesVinculadas = [];
        foreach ($distribuicoes as $distribuicao) {
            $universidadeId = $distribuicao instanceof \app\modules\common\models\DistribuicaoCusto
                ? $distribuicao->universidade_id
                : ($distribuicao['universidade_id'] ?? null);
            if ($universidadeId) {
                $universidadesVinculadas[] = (int) $universidadeId;
            }
        }

        return $this->render($model->isNewRecord ? 'create' : 'update', [
            'model' => $model,
            'title' => $title,
            'trotes' => $this->service->getTrotes(),
            'universidades' => $this->service->getUniversidades($universidadesVinculadas),
            'tiposCategoria' => $this->service->getTiposCategoria($model->tipo_categoria_custo_id ? [(int) $model->tipo_categoria_custo_id] : []),
            'distribuicoes' => $distribuicoes,
        ]);
    }

    private function findModel(int $id): CategoriaCusto
    {
        $model = $this->service->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException('Custo nao encontrado.');
        }
        return $model;
    }
}
