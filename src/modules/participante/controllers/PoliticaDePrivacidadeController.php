<?php

namespace app\modules\participante\controllers;

use Yii;
use yii\web\Controller;

class PoliticaDePrivacidadeController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'politica-de-privacidade';

        if (!Yii::$app->user->isGuest) {
            return $this->redirectToParticipantHome();
        }

        return $this->render('index');
    }

    protected function redirectToParticipantHome()
    {
        return $this->redirect(['/participante/default/home']);
    }
}