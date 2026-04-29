<?php

namespace app\modules\common\services;

use RuntimeException;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Yii;

/**
 * Responsável por gerar PDF de certificados
 */
class PdfGeneratorService
{
    private const MEMORY_LIMIT = '256M';
    private const EXECUTION_TIME = 120;
    private const PDF_FORMAT = 'A4';
    private const PDF_ORIENTATION = 'L';

    public function generatePdf(string $outputPath, array $pages, string $title = 'Certificado Trote Solidario'): void
    {
        $this->ensureWritableDirectory(dirname($outputPath));
        $tempDir = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'mpdf';
        $this->ensureWritableDirectory($tempDir);

        $previousMemoryLimit = ini_get('memory_limit');
        $previousMaxExecutionTime = ini_get('max_execution_time');
        @ini_set('memory_limit', self::MEMORY_LIMIT);
        @set_time_limit(self::EXECUTION_TIME);

        try {
            $mpdf = $this->createMpdfInstance($tempDir, $title);
            $this->loadBootstrapStyles($mpdf);
            $this->writePages($mpdf, $pages);
            $mpdf->Output($outputPath, Destination::FILE);
        } catch (\Throwable $e) {
            if (is_file($outputPath)) {
                @unlink($outputPath);
            }
            throw $e;
        } finally {
            $this->restorePhpSettings($previousMemoryLimit, $previousMaxExecutionTime);
        }
    }

    private function createMpdfInstance(string $tempDir, string $title): Mpdf
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => self::PDF_FORMAT,
            'orientation' => self::PDF_ORIENTATION,
            'tempDir' => $tempDir,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'default_font' => 'Arial',
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->autoScriptToLang = false;
        $mpdf->autoLangToFont = false;
        $mpdf->SetTitle($title);

        return $mpdf;
    }

    private function loadBootstrapStyles(Mpdf $mpdf): void
    {
        $bootstrapCssPath = Yii::getAlias('@vendor', false);
        if (is_string($bootstrapCssPath) && $bootstrapCssPath !== '') {
            $bootstrapCssPath .= DIRECTORY_SEPARATOR . 'kartik-v'
                . DIRECTORY_SEPARATOR . 'yii2-mpdf'
                . DIRECTORY_SEPARATOR . 'src'
                . DIRECTORY_SEPARATOR . 'assets'
                . DIRECTORY_SEPARATOR . 'kv-mpdf-bootstrap.min.css';

            if (is_file($bootstrapCssPath)) {
                $bootstrapCss = file_get_contents($bootstrapCssPath);
                if (is_string($bootstrapCss) && $bootstrapCss !== '') {
                    $mpdf->WriteHTML($bootstrapCss, 1);
                }
            }
        }

        if (class_exists('\\kartik\\mpdf\\Pdf')) {
            $pdfExtraClass = '\\kartik\\mpdf\\Pdf';
            /** @var object $pdfExtra */
            $pdfExtra = new $pdfExtraClass();
            if (method_exists($pdfExtra, 'getCss')) {
                $extraCss = $pdfExtra->getCss();
                if (is_string($extraCss) && $extraCss !== '') {
                    $mpdf->WriteHTML($extraCss, 1);
                }
            }
        }
    }

    private function writePages(Mpdf $mpdf, array $pages): void
    {
        foreach ($pages as $index => $pageHtml) {
            $pageHtml = $this->sanitizeHtmlForPdf($pageHtml);
            $mpdf->WriteHTML($pageHtml);

            if ($index < count($pages) - 1) {
                $mpdf->AddPage();
            }
        }
    }

    private function sanitizeHtmlForPdf(string $html): string
    {
        if ($html === '') {
            return $html;
        }

        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $html) ?? $html;

        if (!mb_check_encoding($html, 'UTF-8')) {
            $converted = @mb_convert_encoding($html, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252, ASCII');
            if (is_string($converted) && $converted !== '') {
                $html = $converted;
            }
        }

        $iconv = @iconv('UTF-8', 'UTF-8//IGNORE', $html);
        if ($iconv !== false) {
            $html = $iconv;
        }

        return $html;
    }

    private function ensureWritableDirectory(string $directory): void
    {
        $this->ensureDirectoryExists($directory);

        if (!is_writable($directory)) {
            throw new RuntimeException('Diretorio sem permissao de escrita: ' . $directory);
        }
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        if (!@mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException(html_entity_decode('N&atilde;o foi poss&iacute;vel preparar o diret&oacute;rio de certificados: ', ENT_QUOTES | ENT_HTML5, 'UTF-8') . $directory);
        }
    }

    private function restorePhpSettings(string|bool $previousMemoryLimit, string|bool $previousMaxExecutionTime): void
    {
        if ($previousMemoryLimit !== false) {
            @ini_set('memory_limit', (string) $previousMemoryLimit);
        }

        if ($previousMaxExecutionTime !== false) {
            @ini_set('max_execution_time', (string) $previousMaxExecutionTime);
        }
    }
}
