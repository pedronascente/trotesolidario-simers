<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'src/vendor/autoload.php';

$path = realpath('src/web/img/logo-site-2025.png');
$data = file_get_contents($path);
$base64 = 'data:image/png;base64,' . base64_encode($data);
$fileUri = 'file:' . str_replace('\\', '/', $path);
$plainPath = $path;
$tempDir = rtrim(getenv('TEMP') ?: sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'trotesolidario-mpdf';
$outDir = getcwd() . DIRECTORY_SEPARATOR . 'tmp';

$cases = [
    'no_image' => '<html><body><div>sem imagem</div></body></html>',
    'base64' => '<html><body><img src="' . $base64 . '" style="width:300px"></body></html>',
    'file_uri' => '<html><body><img src="' . $fileUri . '" style="width:300px"></body></html>',
    'plain_path' => '<html><body><img src="' . str_replace('\\', '\\\\', $plainPath) . '" style="width:300px"></body></html>',
    'var' => '<html><body><img src="var:logo" style="width:300px"></body></html>',
];

foreach ($cases as $name => $html) {
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'tempDir' => $tempDir,
    ]);
    $mpdf->showImageErrors = true;
    if ($name === 'var') {
        $mpdf->imageVars['logo'] = $data;
    }
    $output = $outDir . DIRECTORY_SEPARATOR . 'mpdf-case-' . $name . '.pdf';
    $mpdf->WriteHTML($html);
    $mpdf->Output($output, \Mpdf\Output\Destination::FILE);
    echo $name . '=' . filesize($output) . PHP_EOL;
}
