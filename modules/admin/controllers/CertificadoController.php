<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Doacao;
use app\modules\admin\models\DoacaoSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\modules\admin\models\Helper;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;

/**
 * DoacaoController implements the CRUD actions for Doacao model.
 */
class CertificadoController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete', 'create', 'index', 'update'],
                'rules' => [
                    [
                        'actions' => ['delete', 'create', 'index', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Doacao models.
     * @return mixed
     */
    public function actionIndex() {
        $this->layout = 'admin';
        $connection = Yii::$app->getDb();
        $certificados = $connection
                ->createCommand('SELECT 
                            DISTINCT(t.nome) as trote,u.name 
                            FROM _doacao AS d 
                            INNER JOIN _users u ON d.user_create = u.id 
                            INNER JOIN _trote t on d.trote_id = t.id
                            WHERE d.user_create = "'.Yii::$app->user->identity->id.'"
                            AND d.validado = "1"
                            and d.ativo = "1"
                            AND t.ativo = "1"'
                 )
                ->queryAll();


        return $this->render('index', [
                    'certificados' => $certificados
        ]);
    }

    public function actionImprime() {
        $this->layout = 'admin';
        $connection = Yii::$app->getDb();
        $certificados = $connection
                ->createCommand('SELECT 
                            DISTINCT(t.nome) as trote,
                            t.frase_certificado,
                            u.name 
                            FROM _doacao AS d 
                            INNER JOIN _users u ON d.user_create = u.id 
                            INNER JOIN _trote t on d.trote_id = t.id
                            WHERE d.user_create = "'.Yii::$app->user->identity->id.'"
                            AND d.validado = "1"
                            AND t.nome = "'.$_GET["trote"].'"
                            and d.ativo = "1"
                            AND t.ativo = "1"'
                 )
                ->queryOne();
        
        if(!$certificados){
            return false;
        }
        
        
        $pdf = new \Mpdf\Mpdf([
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'mode' => 'utf-8',
            'format' => 'A4',
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
        ]);
        $pdf->showImageErrors = true;
        $pdfExtra = new Pdf();
        
        $pdf->WriteHTML($pdfExtra->getCss(), 1);
        

        
        $pdf->setAutoTopMargin = 'stretch';
        $pdf->SetTitle('Trote Solidario');
        $htmlContent = $this->renderPartial('certificado', ['model' => $certificados]);
        $pdf->WriteHTML($htmlContent);
        $pdf->AddPage();
        $pdf->SetTitle('Trote Solidario');
        $htmlContent = $this->renderPartial('certificado2', ['model' => $certificados]);
        $pdf->WriteHTML($htmlContent);
        $pdf->defaultPagebreakType = '1';
        return $pdf->output();
    }

    /**
     * Finds the Doacao model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Doacao the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Doacao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
