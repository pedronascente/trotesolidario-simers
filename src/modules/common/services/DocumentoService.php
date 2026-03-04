<?php

namespace app\modules\common\services;

use Yii; 
use yii\web\UploadedFile;
use app\modules\common\models\Documento;
use app\modules\common\services\FileStorageService;
use app\modules\common\services\contracts\DocumentoServiceInterface;

class DocumentoService implements DocumentoServiceInterface
{
    private string $uploadPath;

    public function __construct(private FileStorageService $fileStorage)
    {
        $this->uploadPath = Yii::getAlias('@pdf');
    }

	public function create(Documento $documento):bool
    {
		return  Yii::$app->db->transaction(function() use ($documento){
			
            $arquivoPdf = UploadedFile::getInstance($documento, 'file');
            
            if($arquivoPdf)
            {
                $documento->arquivo = $this->fileStorage->save($arquivoPdf, $this->uploadPath);
         	}

	        return $documento->save();
		});
	}

	public function update(Documento $documento): bool
    {

        return Yii::$app->db->transaction(function () use ($documento) 
        {
            $arquivoPDF = UploadedFile::getInstance($documento, 'file');
            $arquivoPdfAntigo = $documento->arquivo ?? null;

            if ($arquivoPDF) 
            {
                $documento->arquivo = $this->fileStorage->save($arquivoPDF,$this->uploadPath,$arquivoPdfAntigo);
            }

            return $documento->save();
        });
    }

    public function delete(Documento $documento): bool
    {
        return Yii::$app->db->transaction(function () use ($documento) 
        {
            $arquivoPdfAntigo = $documento->arquivo ?? null;

            if ($documento->delete() === false) 
            {
                Yii::error('Erro ao deletar documento ID: ' . $documento->id);
                return false;
            }

            if ($arquivoPdfAntigo) 
            {
                $this->fileStorage->remove($this->uploadPath . '/' . $arquivoPdfAntigo);
            }

            return true;
        });
    }
}