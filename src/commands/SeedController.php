<?php

namespace app\commands;

use app\commands\seeds\BannerSeed;
use app\commands\seeds\CertificadoSeed;
use app\commands\seeds\DoacaoSeed;
use app\commands\seeds\DocumentosSeed;
use app\commands\seeds\EventoSeed;
use app\commands\seeds\ParticipacaoSeed;
use app\commands\seeds\ParticipanteSeed;
use app\commands\seeds\RankingCacheSeed;
use app\commands\seeds\TipoDoacaoSeed;
use app\commands\seeds\TroteSeed;
use app\commands\seeds\UniversidadeSeed;
use app\commands\seeds\UserSeed;
use yii\console\Controller;
use yii\console\ExitCode;

class SeedController extends Controller
{
    /**
     * @return string[]
     */
    protected function getSeedClasses()
    {
        return [
            UserSeed::class,
            UniversidadeSeed::class,
            ParticipanteSeed::class,
            TroteSeed::class,
            ParticipacaoSeed::class,
            TipoDoacaoSeed::class,
            EventoSeed::class,
            DoacaoSeed::class,
            CertificadoSeed::class,
            RankingCacheSeed::class,
            DocumentosSeed::class,
            BannerSeed::class,
        ];
    }

    public function actionIndex()
    {
        return $this->actionList();
    }

    public function actionList()
    {
        $this->stdout("Seeds disponiveis:\n");

        foreach ($this->getSeedClasses() as $seedClass) {
            $this->stdout('- ' . $seedClass::seedName() . "\n");
        }

        return ExitCode::OK;
    }

    public function actionAll()
    {
        foreach ($this->getSeedClasses() as $seedClass) {
            $this->runSeed($seedClass);
        }

        $this->stdout("Todas as seeds foram executadas.\n");

        return ExitCode::OK;
    }

    public function actionRun($name)
    {
        foreach ($this->getSeedClasses() as $seedClass) {
            if ($seedClass::seedName() === $name) {
                $this->runSeed($seedClass);

                return ExitCode::OK;
            }
        }

        $this->stderr("Seed nao encontrada: {$name}\n");

        return ExitCode::UNSPECIFIED_ERROR;
    }

    /**
     * @param class-string<\app\commands\seeds\BaseSeed> $seedClass
     */
    protected function runSeed($seedClass)
    {
        $this->stdout('Executando ' . $seedClass::seedName() . "...\n");

        $seed = new $seedClass();
        $seed->run();

        $this->stdout('Concluida ' . $seedClass::seedName() . ".\n");
    }
}
