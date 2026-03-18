<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use app\modules\common\models\Doacao;
use app\modules\common\models\DoacaoSearchModel;
use app\modules\common\services\contracts\DoacaoServiceInterface;
class DoacaoController extends Controller
{
    private DoacaoServiceInterface $service;

    public function __construct(
        $id,
        $module,
        DoacaoServiceInterface $service,
        $config = []
    ) {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
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
        $this->layout = 'adminsemjquery';
        return parent::beforeAction($action);
    }
 
    public function actionIndex()
    {
        $searchModel = new DoacaoSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel', 'dataProvider'));
        
    }

    public function actionCreate()
    {
        $model = new Doacao();
        $data = $this->service->getFormData();

        if ($model->load(Yii::$app->request->post())) {
            if ($this->service->create($model)) {
                Yii::$app->session->setFlash('success', 'Doação criada com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao criar Doação');
        }

        return $this->render('create', array_merge(['model' => $model], $data));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = $this->service->getFormData();

        if ($model->load(Yii::$app->request->post())) {
            if ($this->service->update($model)) {
                Yii::$app->session->setFlash('success', 'Doação atualizada com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Erro ao atualizar Doação');
        }

        return $this->render('update', array_merge(['model' => $model], $data));
    }
    
    public function actionEventosByTrote()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];

        if ($parents = Yii::$app->request->post('depdrop_parents')) {
            $eventos = $this->service->getEventosByTrote($parents[0]);

            foreach ($eventos as $evento) {
                $out[] = [
                    'id' => $evento['id'],
                    'name' => $evento['nome']
                ];
            }
        }

        return ['output' => $out, 'selected' => ''];
    }

    public function actionTiposDisponiveis($user_id, $trote_id)
    {
        $tipos = $this->service->getTiposDisponiveis($user_id, $trote_id);

        return Json::encode([
            'output' => ArrayHelper::map($tipos, 'id', 'nome')
        ]);
    }

    public function actionAprovar($id)
    {
        $this->service->aprovar($id);
        return $this->redirect(['index']);
    }

    public function actionReprovar()
    {
        $this->service->reprovar(
            Yii::$app->request->post('id'),
            Yii::$app->request->post('observacao')
        );

        return true;
    }

    protected function findModel($id)
    {
        if (($model = Doacao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException();
    }
}
