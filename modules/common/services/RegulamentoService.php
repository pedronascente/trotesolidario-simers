<?php

namespace app\modules\common\services;

use Yii;
use yii\web\UploadedFile;
use app\modules\common\models\Regulamento;
use app\modules\common\services\contracts\RegulamentoServiceInterface;
use app\modules\common\services\FileStorageService;

class RegulamentoService implements RegulamentoServiceInterface{

    private string $uploadPath;

    public function __construct(private FileStorageService $fileStorage) {
        $this->uploadPath = Yii::getAlias('@pdf');
    }

    public function create(Regulamento $regulamento): bool{
        
        return Yii::$app->db->transaction(function () use ($regulamento) {

            $arquivoPdf = UploadedFile::getInstance($regulamento, 'file');

            if ($arquivoPdf) {
                $regulamento->arquivo = $this->fileStorage->save($arquivoPdf, $this->uploadPath);
            }

            return $regulamento->save();
        });
    }

    public function update(Regulamento $regulamento): bool{

        return Yii::$app->db->transaction(function () use ($regulamento) {

            $arquivoPDF = UploadedFile::getInstance($regulamento, 'file');
            $arquivoPdfAntigo = $regulamento->arquivo ?? null;

            if ($arquivoPDF) {
                $regulamento->arquivo = $this->fileStorage->save($arquivoPDF, $this->uploadPath, $arquivoPdfAntigo);
            }

            return $regulamento->save();
        });
    }

    public function delete(Regulamento $regulamento): bool{

        return Yii::$app->db->transaction(function () use ($regulamento) {

            $arquivoPdfAntigo = $regulamento->arquivo ?? null;

            if ($regulamento->delete() === false) {
                Yii::error('Erro ao deletar regulamento ID: ' . $regulamento->id);
                return false;
            }

            if ($arquivoPdfAntigo) {
                $this->fileStorage->remove($this->uploadPath . '/' . $arquivoPdfAntigo);
            }

            return true;
        });
    }
}
