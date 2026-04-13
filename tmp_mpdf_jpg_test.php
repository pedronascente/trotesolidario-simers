<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'src/vendor/autoload.php';
$path = realpath('src/web/img/logocertificado.jpg');
$data = file_get_contents($path);
$base64 = 'data:image/jpeg;base64,' . base64_encode($data);
$tempDir = rtrim(getenv('TEMP') ?: sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'trotesolidario-mpdf';
$outDir = getcwd() . DIRECTORY_SEPARATOR . 'tmp';
$cases = [
    'jpg_base64' => '<html><body><img src="' . $base64 . '" style="width:300px"></body></html>',
    'jpg_var' => '<html><body><img src="var:logo" style="width:300px"></body></html>',
];
foreach ($cases as $name => $html) {
    $mpdf = new \Mpdf\Mpdf(['tempDir' => $tempDir]);
    $mpdf->showImageErrors = true;
    if ($name === 'jpg_var') {
        $mpdf->imageVars['logo'] = $data;
    }
    $out = $outDir . DIRECTORY_SEPARATOR . $name . '.pdf';
    $mpdf->WriteHTML($html);
    $mpdf->Output($out, \Mpdf\Output\Destination::FILE);
    echo $name . '=' . filesize($out) . PHP_EOL;
}
