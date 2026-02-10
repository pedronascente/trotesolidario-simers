<?php

namespace app\modules\common\services;

use Yii; 
use yii\web\UploadedFile;
use app\modules\common\models\Informativo;
use app\modules\common\services\contracts\InformativoServiceInterface;
use app\modules\common\services\FileStorageService;

class InformativoService implements InformativoServiceInterface{

    private string $uploadPath;

    public function __construct(private FileStorageService $fileStorage){
        $this->uploadPath = Yii::getAlias('@pdf');
    }

	public function create(Informativo $informativo):bool{
		return Yii::$app->db->transaction(function() use ($informativo){
			
            $arquivoPdf = UploadedFile::getInstance($informativo, 'file');
            
            if($arquivoPdf){
     			$informativo->arquivo = $this->fileStorage->save($arquivoPdf, $this->uploadPath);
         	}

	        return $informativo->save();
		});
	}

	public function update(Informativo $informativo): bool{

        return Yii::$app->db->transaction(function () use ($informativo) {

            $arquivoPDF = UploadedFile::getInstance($informativo, 'file');
            $arquivoPdfAntigo = $informativo->arquivo ?? null;

            if ($arquivoPDF) {
                $informativo->arquivo = $this->fileStorage->save($arquivoPDF,$this->uploadPath,$arquivoPdfAntigo);
            }

            return $informativo->save();
        });
    }

    public function delete(Informativo $informativo): bool{

        return Yii::$app->db->transaction(function () use ($informativo) {

            $arquivoPdfAntigo = $informativo->arquivo ?? null;

            if ($informativo->delete() === false) {
                Yii::error('Erro ao deletar informativo ID: ' . $informativo->id);
                return false;
            }

            if ($arquivoPdfAntigo) {
                $this->fileStorage->remove($this->uploadPath . '/' . $arquivoPdfAntigo);
            }

            return true;
        });
    }
}