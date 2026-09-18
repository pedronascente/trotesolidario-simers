<?php

namespace app\modules\common\services;

use Yii;
use yii\web\UploadedFile;

final class DoacaoArquivoStorage
{
    public static function resolve(?string $arquivo): ?string
    {
        if (!$arquivo || basename($arquivo) !== $arquivo) {
            return null;
        }

        $directory = Yii::getAlias('@imgArquivosDoacao');
        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $arquivo;

        return is_file($path) ? $path : null;
    }

    public static function isImage(?string $path): bool
    {
        return $path !== null && @getimagesize($path) !== false;
    }

    public static function save(UploadedFile $arquivo): ?string
    {
        $directory = Yii::getAlias('@imgArquivosDoacao');
        if (!self::ensureWritableDirectory($directory)) {
            Yii::error('Diretorio de comprovantes inexistente ou sem permissao de escrita: ' . $directory, __METHOD__);
            return null;
        }

        $name = uniqid('doacao_', true) . '.' . strtolower($arquivo->extension);
        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;

        if (!$arquivo->saveAs($path)) {
            Yii::error('Falha ao salvar comprovante no diretorio: ' . $directory, __METHOD__);
            return null;
        }

        return $name;
    }

    public static function remove(?string $arquivo): bool
    {
        $path = self::resolve($arquivo);
        if ($path === null) {
            return true;
        }

        if (@unlink($path)) {
            return true;
        }

        Yii::warning('Nao foi possivel remover o comprovante de doacao: ' . basename($path), __METHOD__);
        return false;
    }

    private static function ensureWritableDirectory(string $path): bool
    {
        if (!is_dir($path) && !@mkdir($path, 0775, true) && !is_dir($path)) {
            return false;
        }

        return is_writable($path);
    }
}
