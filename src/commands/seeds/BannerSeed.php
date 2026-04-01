<?php

namespace app\commands\seeds;

class BannerSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'banner';
    }

    public function run()
    {
        $now = $this->now();

        $this->upsertRows('{{%banner}}', [
            [
                'id' => 1,
                'tipo' => 'home_principal',
                'img_dsk' => 'banners/home-desktop.jpg',
                'img_mob' => 'banners/home-mobile.jpg',
                'ativo' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'tipo' => 'campanha_doacao',
                'img_dsk' => 'banners/doacao-desktop.jpg',
                'img_mob' => 'banners/doacao-mobile.jpg',
                'ativo' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'tipo' => 'certificados',
                'img_dsk' => 'banners/certificados-desktop.jpg',
                'img_mob' => 'banners/certificados-mobile.jpg',
                'ativo' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}