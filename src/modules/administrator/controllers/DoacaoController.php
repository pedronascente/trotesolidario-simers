<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\modules\common\models\Doacao;
use app\modules\common\models\DoacaoSearchModel;
use app\modules\common\services\contracts\DoacaoServiceInterface;

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
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }

                    throw new ForbiddenHttpException('Acesso negado');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'aprovar' => ['POST'],
                    'reprovar' => ['POST'],
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
                Yii::$app->session->setFlash('success', 'Doacao criada com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', $this->getModelErrorMessage($model, 'Erro ao criar doacao.'));
        }

        return $this->render('create', array_merge(['model' => $model], $data));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->status === Doacao::STATUS_APROVADA) {
            throw new ForbiddenHttpException('Doacoes aprovadas nao podem ser editadas.');
        }

        $data = $this->service->getFormData();

        if (!isset($data['participacoes'][$model->participacao_id])) {
            $participacaoAtual = $model->participacao;

            if ($participacaoAtual !== null) {
                $data['participacoes'][$participacaoAtual->id] = $participacaoAtual->getDisplayLabel();
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($this->service->update($model)) {
                Yii::$app->session->setFlash('success', 'Doacao atualizada com sucesso');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', $this->getModelErrorMessage($model, 'Erro ao atualizar doacao.'));
        }

        return $this->render('update', array_merge(['model' => $model], $data));
    }

    public function actionEventosByParticipacao()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];

        if ($parents = Yii::$app->request->post('depdrop_parents')) {
            $eventos = $this->service->getEventosByParticipacao((int) $parents[0]);

            foreach ($eventos as $evento) {
                $out[] = [
                    'id' => $evento['id'],
                    'name' => $evento['nome'],
                ];
            }
        }

        return ['output' => $out, 'selected' => ''];
    }

    public function actionAprovar($id)
    {
        if ($this->service->aprovar((int) $id)) {
            Yii::$app->session->setFlash('success', 'Doacao aprovada e certificado atualizado em PDF.');
        } else {
            Yii::$app->session->setFlash('error', 'Nao foi possivel aprovar a doacao e gerar o certificado.');
        }

        return $this->redirect(['index']);
    }

    public function actionReprovar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'success' => $this->service->reprovar(
                (int) Yii::$app->request->post('id'),
                (string) Yii::$app->request->post('motivo_reprovado')
            ),
        ];
    }

    protected function findModel($id): Doacao
    {
        if (($model = Doacao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException();
    }

    private function getModelErrorMessage(Doacao $model, string $fallback): string
    {
        $errors = $model->getFirstErrors();
        return !empty($errors) ? implode(' ', $errors) : $fallback;
    }
}
