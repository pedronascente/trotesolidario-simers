<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\TipoCategoriaCusto;
use app\modules\common\models\TipoCategoriaCustoSearchModel;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class TipoCategoriaCustoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@'], 'matchCallback' => static fn() => Yii::$app->user->identity->isAdmin()]],
                'denyCallback' => static function () {
                    if (Yii::$app->user->isGuest) return Yii::$app->response->redirect(['/auth/login']);
                    throw new ForbiddenHttpException('Acesso negado');
                },
            ],
            'verbs' => ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]],
        ];
    }

    public function beforeAction($action) { $this->layout = 'adminsemjquery'; return parent::beforeAction($action); }

    public function actionIndex()
    {
        $searchModel = new TipoCategoriaCustoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionCreate() { return $this->saveForm(new TipoCategoriaCusto()); }
    public function actionUpdate($id) { return $this->saveForm($this->findModel((int) $id)); }

    public function actionDelete($id)
    {
        $model = $this->findModel((int) $id);
        try {
            if ($model->getCustos()->exists()) {
                Yii::$app->session->setFlash('error', 'A categoria possui custos vinculados e nao pode ser excluida. Desative-a.');
            } else {
                $model->delete();
                Yii::$app->session->setFlash('success', 'Categoria excluida com sucesso.');
            }
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
            Yii::$app->session->setFlash('error', 'Nao foi possivel excluir a categoria.');
        }
        return $this->redirect(['index']);
    }

    private function saveForm(TipoCategoriaCusto $model)
    {
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Categoria salva com sucesso.');
            return $this->redirect(['index']);
        }
        return $this->render($model->isNewRecord ? 'create' : 'update', compact('model'));
    }

    private function findModel(int $id): TipoCategoriaCusto
    {
        if (($model = TipoCategoriaCusto::findOne($id)) !== null) return $model;
        throw new NotFoundHttpException('Categoria de custo nao encontrada.');
    }
}
