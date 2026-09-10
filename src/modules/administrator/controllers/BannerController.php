<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\modules\common\models\Banner;
use app\modules\common\models\BannerSearchModel;
use app\modules\common\services\contracts\BannerServiceInterface;

class BannerController extends Controller{

    private BannerServiceInterface $service;

    public function __construct(
        $id,
        $module,
        BannerServiceInterface $service,$config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // precisa estar logado
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    // não logado → login
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }

                    // logado mas não admin → 403
                    throw new \yii\web\ForbiddenHttpException('Acesso negado');
                }
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'toggle' => ['POST'],
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
        $searchModel  = new BannerSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $locaisDisponiveis = $this->getLocaisDisponiveis();

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider'=> $dataProvider,
            'podeCadastrar' => $locaisDisponiveis !== [],
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $locaisDisponiveis = $this->getLocaisDisponiveis();
        if ($locaisDisponiveis === []) {
            Yii::$app->session->setFlash('error', 'Os dois locais de exibição de banners já estão cadastrados.');
            return $this->redirect(['index']);
        }

        $banner = new Banner();

        if ($banner->load(Yii::$app->request->post()))
        {
            try {
                if ($this->service->create($banner)) {
                    Yii::$app->session->setFlash('success', 'Banner criado com sucesso.');
                    return $this->redirect(['index']);
                }
            } catch (\Throwable $e) {
                Yii::error('Falha ao criar banner: ' . $e->getMessage(), __METHOD__);
                $banner->addError('', 'Não foi possível armazenar o banner. Tente novamente.');
            }
        }

        return $this->render('create', [
            'model' => $banner,
            'locaisExibicao' => $locaisDisponiveis,
        ]);
    }

    public function actionUpdate($id)
    {
        $banner = $this->findModel($id);
        $locaisExibicao = $this->getLocaisDisponiveis($banner->id);

        if ($banner->load(Yii::$app->request->post()))
        {
            try {
                if ($this->service->update($banner)) {
                    Yii::$app->session->setFlash('success', 'Banner atualizado com sucesso.');
                    return $this->redirect(['index']);
                }
            } catch (\Throwable $e) {
                Yii::error('Falha ao atualizar banner ID ' . $banner->id . ': ' . $e->getMessage(), __METHOD__);
                $banner->addError('', 'Não foi possível armazenar as alterações. Tente novamente.');
            }
        }

        return $this->render('update', [
            'model' => $banner,
            'locaisExibicao' => $locaisExibicao,
        ]);
    }

    public function actionDelete($id)
    {
        $banner = $this->findModel($id);

        try {
            if ($this->service->delete($banner)) {
                Yii::$app->session->setFlash('success', 'Banner excluído com sucesso.');
            } else {
                Yii::$app->session->setFlash('error', implode(' ', $banner->getFirstErrors()) ?: 'Erro ao excluir banner.');
            }
        } catch (\Throwable $e) {
            Yii::error('Falha ao excluir banner ID ' . $banner->id . ': ' . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'Não foi possível concluir a exclusão do banner.');
        }

        return $this->redirect(['index']);
    }

    public function actionToggle($id)
    {
        $banner = $this->findModel($id);
        $banner->ativo = (int) $banner->ativo === 1 ? 0 : 1;

        try {
            if ($this->service->update($banner)) {
                Yii::$app->session->setFlash('success', $banner->ativo ? 'Banner ativado com sucesso.' : 'Banner desativado com sucesso.');
            } else {
                Yii::$app->session->setFlash('error', implode(' ', $banner->getFirstErrors()) ?: 'Não foi possível alterar o status do banner.');
            }
        } catch (\Throwable $e) {
            Yii::error('Falha ao alterar status do banner ID ' . $banner->id . ': ' . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'Não foi possível alterar o status do banner.');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id): Banner
    {
        if (($model = Banner::findOne($id)) !== null)
        {
            return $model;
        }
        throw new NotFoundHttpException('Banner não encontrado.');
    }

    private function getLocaisDisponiveis(?int $ignorarBannerId = null): array
    {
        $query = Banner::find()->select('tipo');
        if ($ignorarBannerId !== null) {
            $query->andWhere(['!=', 'id', $ignorarBannerId]);
        }

        $locaisOcupados = $query->column();

        return array_diff_key(
            Banner::getLocaisExibicao(),
            array_fill_keys($locaisOcupados, true)
        );
    }
}
