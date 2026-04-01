<?php
require 'src/vendor/autoload.php';
require 'src/vendor/yiisoft/yii2/Yii.php';
$config = require 'src/config/web.php';
$app = new yii\web\Application($config);
$cert = app\modules\common\models\Certificado::find()
    ->with(['participacao.user','participacao.trote','participacao.universidade','participacao.doacoes.tipoDoacao'])
    ->where(['participacao_id' => 1])
    ->one();
if ($cert === null) {
    echo "CERT_NOT_FOUND\n";
    exit(0);
}
echo Yii::$app->view->renderFile(Yii::getAlias('@app/modules/common/views/certificado/template.php'), ['model' => $cert]);
