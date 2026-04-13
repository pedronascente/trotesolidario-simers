<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('YII_DEBUG', true);
define('YII_ENV', 'dev');

require 'src/vendor/autoload.php';
require 'src/vendor/yiisoft/yii2/Yii.php';

$config = require 'src/config/web.php';
new yii\web\Application($config);

$model = [
    'name' => 'Carla Comissao',
    'trote' => '2026/1',
    'qualidade' => 'PARTICIPANTE',
    'total_horas' => 15,
    'frase_certificado' => 'nos dias 1 de fevereiro de 2026 ? 30 de abril de 2026, com carga hor?ria total de',
    'all_donations' => ['Medula ?ssea'],
];

$html = Yii::$app->view->renderFile(
    Yii::getAlias('@app/modules/participante/views/certificado/certificado202611.php'),
    ['model' => $model, 'renderMode' => 'pdf']
);

preg_match_all('/<img[^>]+src="([^"]+)"/i', $html, $matches);
echo 'html_image_tags=' . count($matches[1]) . PHP_EOL;
foreach ($matches[1] as $src) {
    echo 'src=' . $src . PHP_EOL;
}

$tempDir = rtrim(getenv('TEMP') ?: sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'trotesolidario-mpdf';
$mpdf = new Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L',
    'tempDir' => $tempDir,
    'margin_left' => 0,
    'margin_right' => 0,
    'margin_top' => 0,
    'margin_bottom' => 0,
    'default_font' => 'Arial',
]);
$mpdf->WriteHTML($html);

$output = getcwd() . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'test-certificado-2026-1.pdf';
$mpdf->Output($output, \Mpdf\Output\Destination::FILE);

$pdf = file_get_contents($output);
$imageCount = $pdf === false ? 0 : substr_count($pdf, '/Subtype /Image');
$size = is_file($output) ? filesize($output) : 0;

echo 'output=' . $output . PHP_EOL;
echo 'size=' . $size . PHP_EOL;
echo 'image_objects=' . $imageCount . PHP_EOL;
