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
                'tipo' => 'Login',
                'img_dsk' => '69e623088bd24.png',
                'img_mob' => '69e623088bec9.png',
                'ativo' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}