<?php

namespace app\modules\common\services;

use Yii;
use yii\web\UploadedFile;
use app\modules\common\models\Universidade;
use app\modules\common\services\contracts\UniversidadeServiceInterface;

class UniversidadeService implements UniversidadeServiceInterface{

	public function create(Universidade $universidade):bool{
		return Yii::$app->db->transaction(function() use ($universidade){
			$arquivo = UploadedFile::getInstance($universidade, 'file');
			if($arquivo){
     			$universidade->icon = $this->saveIcon($arquivo);
         	}
		
	        if(!$universidade->save()){
	         	return false;
	        }
	        return true;
		});
	}

	public function update(Universidade $universidade): bool{
        return Yii::$app->db->transaction(function () use ($universidade) {
            $arquivo = UploadedFile::getInstance($universidade, 'file');
            $arquivoAntigo = $universidade->icon ?? null;

            if ($arquivo) {
                $universidade->icon = $this->saveIcon($arquivo);
                $this->removeIcon($arquivoAntigo);
            }
            return $universidade->save();
        });
    }

    public function toggleAtivo(Universidade $universidade): bool {
        $universidade->ativo = !$universidade->ativo;
        return $universidade->save(false);
    }

    protected function saveIcon(UploadedFile $file): string {
        $dir = Yii::getAlias('@webroot/img/');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $nome = uniqid('uni_') . '.' . $file->extension;
        $file->saveAs($dir . $nome);

        return $nome;
    }

    protected function removeIcon(?string $icon): void  {
        if ($icon) {
            $path = Yii::getAlias('@webroot/img/') . $icon;
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}