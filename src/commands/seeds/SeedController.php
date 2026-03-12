<?php

namespace app\commands\seeds;

use yii\console\Controller;

class SeedController extends Controller
{
    public function actionRun()
    {
        echo "Rodando UserSeed...\n";
        $this->run('seeds/user-seed/run');

        echo "Rodando TipoDoacaoSeed...\n";
        $this->run('seeds/tipo-doacao-seed/run');

        echo "Rodando TroteSeed...\n";
        $this->run('seeds/trote-seed/run');

        echo "Rodando EventoSeed...\n";
        $this->run('seeds/evento-seed/run');

        echo "Rodando UniversidadeSeed...\n";
        $this->run('seeds/universidade-seed/run');

        echo "Rodando DoacaoSeed...\n";
        $this->run('seeds/doacao-seed/run');

        echo "Seeds finalizados!\n";
    }
}
