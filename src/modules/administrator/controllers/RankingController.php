<?php

namespace app\modules\administrator\controllers;

use app\modules\common\models\RankingCacheSearchModel;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class RankingController extends Controller
{
    private RankingCacheServiceInterface $service;

    public function __construct($id, $module, RankingCacheServiceInterface $service, $config = [])
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
                    'rebuild' => ['POST'],
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
        $searchModel = new RankingCacheSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $selectedTroteId = Yii::$app->request->get('trote_id');
        $selectedTroteId = $selectedTroteId !== null && $selectedTroteId !== '' ? (int) $selectedTroteId : null;

        if ($selectedTroteId !== null) {
            $dataProvider->query->andWhere(['ranking_cache.trote_id' => $selectedTroteId]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'trotes' => $this->service->findTrotes(),
            'selectedTroteId' => $selectedTroteId,
            'universityRanking' => $this->service->getUniversityRanking($selectedTroteId),
        ]);
    }

    public function actionRebuild()
    {
        $troteId = Yii::$app->request->post('trote_id');
        $troteId = $troteId !== null && $troteId !== '' ? (int) $troteId : null;

        $total = $this->service->rebuild($troteId);
        Yii::$app->session->setFlash('success', 'Ranking recalculado com sucesso. Registros atualizados: ' . $total . '.');

        $params = [];
        if ($troteId !== null) {
            $params['trote_id'] = $troteId;
        }

        return $this->redirect(array_merge(['index'], $params));
    }
}
