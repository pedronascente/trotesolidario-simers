<?php

namespace app\modules\common\services;

use Yii;
use yii\web\UploadedFile;

class FileStorageService{
    
    public function save(UploadedFile $file, string $path, ?string $oldFile = null): string {
        
        if ($oldFile) {
            $this->remove($path . '/' . $oldFile);
        }

        $filename = uniqid() . '.' . $file->extension;
        $file->saveAs($path . '/' . $filename);

        return $filename;
    }

    public function remove(string $fullPath): void{
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
