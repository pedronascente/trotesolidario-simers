<?php

namespace app\modules\participante\controllers;

use Yii;
use app\modules\common\models\Doacao;
use app\modules\common\models\DoacaoSearchModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\modules\common\models\Helper;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;

/**
 * DoacaoController implements the CRUD actions for Doacao model.
 */
class CertificadoController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete', 'create', 'index', 'update', 'imprime'],
                'rules' => [
                    [
                        'actions' => ['delete', 'create', 'index', 'update', 'imprime'],
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
    public function actionIndex()
    {
        $this->layout = 'participante';
        $connection = Yii::$app->getDb();
        $certificados = $connection
            ->createCommand(
                'SELECT 
                             DISTINCT(t.nome) as trote, d.tipo_doacao, t.id,u.name 
                            FROM _doacao AS d 
                            INNER JOIN _users u ON d.user_create = u.id 
                            INNER JOIN _trote t on d.trote_id = t.id
                            WHERE d.user_create = "' . Yii::$app->user->identity->id . '"
                            AND d.validado = "1"
                            and d.ativo = "1"
                            '
            )
            ->queryAll();


        return $this->render('index', [
            'certificados' => $certificados
        ]);
    }

    public function actionImprime()
    {
        $this->layout = 'participante';
        $connection = Yii::$app->getDb();
        $certificado = $connection
            ->createCommand(
                'SELECT 
                            DISTINCT(t.nome) as trote,
                            t.frase_certificado,
                            d.tipo_doacao,
                            u.name 
                            FROM _doacao AS d 
                            INNER JOIN _users u ON d.user_create = u.id 
                            INNER JOIN _trote t on d.trote_id = t.id
                            WHERE d.user_create = "' . Yii::$app->user->identity->id . '"
                            AND d.validado = "1"
                            AND t.nome = "' . $_GET["trote"] . '"
                            AND d.tipo_doacao = "' . $_GET["tipo_doacao"] . '"
                            and d.ativo = "1"
                            '
            )
            ->queryOne();

        if (!$certificado) {
            return false;
        }
        if (isset($_GET["troteid"]) && $_GET["troteid"] == 9) {
            $allDonations = $connection
                ->createCommand(
                    'SELECT 
                                d.tipo_doacao
                                FROM _doacao AS d 
                                INNER JOIN _trote t on d.trote_id = t.id
                                WHERE d.user_create = "' . Yii::$app->user->identity->id . '"
                                AND d.validado = "1"
                                AND t.id = "' . $_GET["troteid"] . '"
                                AND d.ativo = "1"
                                '
                )
                ->queryAll();

            $totalHours = 0;
            $donationTypes = [];

            foreach ($allDonations as $donation) {
                if (!in_array($donation['tipo_doacao'], $donationTypes)) {
                    $donationTypes[] = $donation['tipo_doacao'];
                }

                switch ($donation['tipo_doacao']) {
                    case 'Comissão':
                    case 'Comissão organizadora':
                        $totalHours = 40;
                        break;
                    case 'Sangue':
                    case 'Medula Óssea':
                        if ($totalHours < 40) {
                            $totalHours += 8;
                        }
                        break;
                    case 'Alimentos':
                        if ($totalHours < 40) {
                            $totalHours += 4;
                        }
                        break;
                    case 'Participação Presencial':
                        if ($totalHours < 40) {
                            $totalHours += 6;
                        }
                        break;
                }

                if ($totalHours > 40) {
                    $totalHours = 40;
                }
            }

            $certificado['total_horas'] = $totalHours;
            $certificado['all_donations'] = $donationTypes;
        }


        $pdf = new \Mpdf\Mpdf([
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'mode' => 'utf-8',
            'format' => 'A4',
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);
        $pdf->showImageErrors = true;
        $pdfExtra = new Pdf();

        $pdf->WriteHTML($pdfExtra->getCss(), 1);



        $pdf->setAutoTopMargin = 'stretch';
        $pdf->SetTitle('Trote Solidario');
        //trote 2021/2
        if ($_GET["troteid"] == 1) {
            $htmlContent = $this->renderPartial('certificado', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 2) {
            $htmlContent = $this->renderPartial('certificado202211', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 3) {
            $htmlContent = $this->renderPartial('certificado202221', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 4 || $_GET["troteid"] == 5) {
            $htmlContent = $this->renderPartial('certificado202311', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 6) {
            $htmlContent = $this->renderPartial('certificado202411', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 7) {
            $htmlContent = $this->renderPartial('certificado202421', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 8 || $_GET["troteid"] == 9) {
            $htmlContent = $this->renderPartial('certificado202521', ['model' => $certificado]);
        }


        $pdf->WriteHTML($htmlContent);
        $pdf->AddPage();
        $pdf->SetTitle('Trote Solidario');
        //trote 2021/2
        if ($_GET["troteid"] == 1) {
            $htmlContent = $this->renderPartial('certificado2', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 3) {
            $htmlContent = $this->renderPartial('certificado202222', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 4 || $_GET["troteid"] == 5) {
            $htmlContent = $this->renderPartial('certificado202312', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 6) {
            $htmlContent = $this->renderPartial('certificado202412', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 7) {
            $htmlContent = $this->renderPartial('certificado202422', ['model' => $certificado]);
        } else if ($_GET["troteid"] == 8 || $_GET["troteid"] == 9) {
            $htmlContent = $this->renderPartial('certificado202511', ['model' => $certificado]);
        } else {
            $htmlContent = $this->renderPartial('certificado202212', ['model' => $certificado]);
        }
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
    protected function findModel($id)
    {
        if (($model = Doacao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}