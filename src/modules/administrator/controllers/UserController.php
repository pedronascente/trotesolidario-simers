<?php

namespace app\modules\administrator\controllers;

use app\models\User;
use app\modules\common\models\UserCreateForm;
use app\modules\common\models\UserSearchModel;
use app\modules\common\services\contracts\ParticipanteServiceInterface;
use app\modules\common\services\contracts\UserServiceInterface;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    private $userService;
    private $participanteService;

    public function __construct($id, $module, UserServiceInterface $userService, ParticipanteServiceInterface $participanteService, $config = [])
    {
        $this->userService = $userService;
        $this->participanteService = $participanteService;
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
                        'matchCallback' => static function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => static function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/auth/login']);
                    }

                    throw new \yii\web\ForbiddenHttpException('Acesso negado.');
                },
            ],
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
        $searchModel = new UserSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionCreate()
    {
        $model = new UserCreateForm();
        $model->scenario = 'create';
        $model->status = User::STATUS_ACTIVE;
        $model->role = User::ROLE_PARTICIPANTE;
        $model->estudante = 1;
        $model->estudante_medicina = 0;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $user = $this->userService->create($model);
                $this->syncParticipante($user->id, $model);
                $transaction->commit();

                Yii::$app->session->setFlash('success', 'Usuario criado com sucesso.');
                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $user = $this->findUser($id);
        $model = new UserCreateForm();
        $model->scenario = 'update';
        $model->id = $user->id;
        $model->nome = $user->nome;
        $model->email = $user->email;
        $model->username = $user->username;
        $model->cpf = $user->cpf;
        $model->role = $user->role;
        $model->status = $user->status;

        $participante = $this->participanteService->findByUserId($user->id);
        if ($participante !== null) {
            $model->create_participante = 1;
            $model->estudante = $participante->estudante;
            $model->estudante_medicina = $participante->estudante_medicina;
            $model->previsao_formatura = $participante->previsao_formatura;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $this->userService->update($user->id, $model);
                $this->syncParticipante($user->id, $model);
                $transaction->commit();

                Yii::$app->session->setFlash('success', 'Usuario atualizado com sucesso.');
                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    public function actionDelete($id)
    {
        $this->userService->delete($id);
        Yii::$app->session->setFlash('success', 'Usuario inativado com sucesso.');

        return $this->redirect(['index']);
    }

    private function findUser($id): User
    {
        $user = $this->userService->findById($id);
        if ($user === null) {
            throw new NotFoundHttpException('Usuario nao encontrado.');
        }

        return $user;
    }

    private function syncParticipante($userId, UserCreateForm $model): void
    {
        $existing = $this->participanteService->findByUserId($userId);

        if ((int) $model->create_participante !== 1) {
            if ($existing !== null) {
                $this->participanteService->delete($existing->id);
            }
            return;
        }

        $payload = [
            'user_id' => $userId,
            'estudante' => (int) $model->estudante,
            'estudante_medicina' => (int) $model->estudante_medicina,
            'previsao_formatura' => $model->previsao_formatura,
        ];

        if ($existing !== null) {
            $this->participanteService->update($existing->id, $payload);
            return;
        }

        $this->participanteService->create($payload);
    }
}
