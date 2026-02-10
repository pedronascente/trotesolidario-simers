<?php

namespace app\modules\common\services;

use Yii;
use yii\web\UploadedFile;
use app\modules\common\models\Banner;
use app\modules\common\services\contracts\BannerServiceInterface;
use app\modules\common\services\FileStorageService;

class BannerService implements BannerServiceInterface{
    
    private string $uploadPath;

    public function __construct(private FileStorageService $fileStorage){
        $this->uploadPath = Yii::getAlias('@img');
    }

    public function create(Banner $banner): bool{
        return Yii::$app->db->transaction(function () use ($banner) {

            $imagemDesktop = UploadedFile::getInstance($banner, 'file_dsk');
            $imagemMobile  = UploadedFile::getInstance($banner, 'file_mob');

            if ($imagemDesktop) {
                $banner->img_dsk = $this->fileStorage->save($imagemDesktop, $this->uploadPath);
            }

            if ($imagemMobile) {
                $banner->img_mob = $this->fileStorage->save($imagemMobile, $this->uploadPath);
            }

            /**
             * REGRA DE NEGÓCIO
             * Se este banner estiver ativo,
             * desativa os outros da mesma posição
             */
           if ((int)$banner->ativo === 1) {
                Banner::updateAll(
                    ['ativo' => 0],
                    [
                        'and',
                        ['posicao' => $banner->posicao],
                        ['!=', 'id', $banner->id]
                    ]
                );
            }


            return $banner->save();
        });
    }

    public function update(Banner $banner): bool{
        return Yii::$app->db->transaction(function () use ($banner) {

            $imagemDesktop = UploadedFile::getInstance($banner, 'file_dsk');
            $imagemMobile  = UploadedFile::getInstance($banner, 'file_mob');

            $imgDskAntiga = $banner->getOldAttribute('img_dsk');
            $imgMobAntiga = $banner->getOldAttribute('img_mob');

            if ($imagemDesktop) {
                $banner->img_dsk = $this->fileStorage->save(
                    $imagemDesktop,
                    $this->uploadPath,
                    $imgDskAntiga
                );
            }

            if ($imagemMobile) {
                $banner->img_mob = $this->fileStorage->save(
                    $imagemMobile,
                    $this->uploadPath,
                    $imgMobAntiga
                );
            }

            /**
             * REGRA DE NEGÓCIO
             * Se este banner estiver ativo,
             * desativa os outros da mesma posição
             */
            if ((int)$banner->ativo === 1) {
                Banner::updateAll(
                    ['ativo' => 0],
                    [
                        'and',
                        ['posicao' => $banner->posicao],
                        ['!=', 'id', $banner->id]
                    ]
                );
            }


            return $banner->save();
        });
    }

    public function delete(Banner $banner): bool{
        return Yii::$app->db->transaction(function () use ($banner) {

            if ($banner->img_dsk) {
                $this->fileStorage->remove($this->uploadPath . '/' . $banner->img_dsk);
            }

            if ($banner->img_mob) {
                $this->fileStorage->remove($this->uploadPath . '/' . $banner->img_mob);
            }

            return (bool)$banner->delete();
        });
    }

}
