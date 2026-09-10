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
        $this->loadUploads($banner);
        if (!$banner->validate()) {
            return false;
        }

        $newFiles = [];
        try {
            $this->storeUploads($banner, $newFiles);
            return Yii::$app->db->transaction(function () use ($banner) {
                $this->deactivateOthersWhenNeeded($banner);
                if (!$banner->save(false)) {
                    throw new \RuntimeException('Não foi possível salvar o banner.');
                }
                return true;
            });
        } catch (\Throwable $e) {
            $this->removeFiles($newFiles);
            throw $e;
        }
    }

    public function update(Banner $banner): bool{
        $this->loadUploads($banner);
        if (!$banner->validate()) {
            return false;
        }

        $oldDesktop = (string) $banner->getOldAttribute('img_dsk');
        $oldMobile = (string) $banner->getOldAttribute('img_mob');
        $newFiles = [];

        try {
            $this->storeUploads($banner, $newFiles);
            Yii::$app->db->transaction(function () use ($banner) {
                $this->deactivateOthersWhenNeeded($banner);
                if (!$banner->save(false)) {
                    throw new \RuntimeException('Não foi possível atualizar o banner.');
                }
            });
        } catch (\Throwable $e) {
            $this->removeFiles($newFiles);
            $banner->img_dsk = $oldDesktop ?: null;
            $banner->img_mob = $oldMobile ?: null;
            throw $e;
        }

        if ($banner->file_dsk !== null && $oldDesktop !== '') {
            $this->fileStorage->remove($this->uploadPath . '/' . basename($oldDesktop));
        }
        if ($banner->file_mob !== null && $oldMobile !== '') {
            $this->fileStorage->remove($this->uploadPath . '/' . basename($oldMobile));
        }

        return true;
    }

    public function delete(Banner $banner): bool{
        if ((int) $banner->ativo === 1) {
            $banner->addError('ativo', 'Desative o banner antes de excluí-lo.');
            return false;
        }

        $desktop = $banner->img_dsk;
        $mobile = $banner->img_mob;
        $deleted = Yii::$app->db->transaction(function () use ($banner) {
            return (bool) $banner->delete();
        });

        if ($deleted && $desktop) {
            $this->fileStorage->remove($this->uploadPath . '/' . basename($desktop));
        }
        if ($deleted && $mobile) {
            $this->fileStorage->remove($this->uploadPath . '/' . basename($mobile));
        }

        return $deleted;
    }

    private function loadUploads(Banner $banner): void
    {
        $banner->file_dsk = UploadedFile::getInstance($banner, 'file_dsk');
        $banner->file_mob = UploadedFile::getInstance($banner, 'file_mob');
    }

    private function storeUploads(Banner $banner, array &$newFiles): void
    {
        if ($banner->file_dsk !== null) {
            $banner->img_dsk = $this->fileStorage->save($banner->file_dsk, $this->uploadPath);
            $newFiles[] = $banner->img_dsk;
        }
        if ($banner->file_mob !== null) {
            $banner->img_mob = $this->fileStorage->save($banner->file_mob, $this->uploadPath);
            $newFiles[] = $banner->img_mob;
        }
    }

    private function deactivateOthersWhenNeeded(Banner $banner): void
    {
        if ((int) $banner->ativo !== 1) {
            return;
        }

        if (Yii::$app->db->driverName === 'mysql') {
            Yii::$app->db->createCommand(
                'SELECT [[id]] FROM {{%banner}} WHERE [[tipo]] = :tipo FOR UPDATE',
                [':tipo' => $banner->tipo]
            )->queryColumn();
        }
        Banner::updateAll(['ativo' => 0], [
            'and',
            ['tipo' => $banner->tipo],
            ['!=', 'id', $banner->id],
        ]);
    }

    private function removeFiles(array $files): void
    {
        foreach ($files as $file) {
            $this->fileStorage->remove($this->uploadPath . '/' . basename($file));
        }
    }

}
