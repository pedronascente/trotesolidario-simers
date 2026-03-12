<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use app\modules\common\models\Doacao;
use app\modules\common\models\DoacaoSearchModel;
use app\modules\common\models\Evento;
use app\modules\common\models\TipoDoacao;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use app\modules\common\models\Users;

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
        $trote = ArrayHelper::map(Trote::find()->where(['status' => 'ativo'])->orderBy('titulo')->all(),'id','titulo');
        $evento = ArrayHelper::map(Evento::find()->orderBy('nome')->all(),'id','nome');
        $usuario = ArrayHelper::map(Users::find()->orderBy('name')->all(),'id','name');
        $universidade = ArrayHelper::map(Universidade::find()->orderBy('nome')->all(), 'id', 'nome');
        $tipoDoacao = ArrayHelper::map(TipoDoacao::find()->orderBy('nome')->all(),'id', 'nome');

        if ($model->load(Yii::$app->request->post())) 
        {
            if ($this->service->create($model)) 
            {
                Yii::$app->session->setFlash('success', 'Doação criada com sucesso');
                return $this->redirect(['index']);
            }
            
            Yii::$app->session->setFlash('error', 'Erro ao criar Doação');
        }

        return $this->render('create', compact(
            'model',
            'trote', 
            'evento',
            'usuario',
            'universidade',
            'tipoDoacao'
        ));
    }

    public function actionAprovar($id)
    {
        $model = Doacao::findOne($id);

        if ($model) {
            $model->status = Doacao::STATUS_APROVADO;
            $model->save(false);
        }

        return $this->redirect(['index']);
    }

    public function actionReprovar($id)
    {
        $model = Doacao::findOne($id);

        if ($model) {
            $model->status = Doacao::STATUS_REJEITADO;
            $model->save(false);
        }

        return $this->redirect(['index']);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($this->service->update($model)) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', compact('model'));
    }

    protected function findModel($id)
    {
        if (($model = Doacao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException();
    }

    public function actionEventosByTrote()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];

        if (isset($_POST['depdrop_parents'])) {

            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {

                $trote_id = $parents[0];

                $eventos = Evento::find()
                    ->where(['trote_id' => $trote_id])
                    ->orderBy('nome')
                    ->all();

                foreach ($eventos as $evento) {
                    $out[] = [
                        'id' => $evento->id,
                        'name' => $evento->nome
                    ];
                }

                return ['output' => $out, 'selected' => ''];
            }
        }

        return ['output' => '', 'selected' => ''];
    }
}
