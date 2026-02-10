<?php

use app\modules\common\services\troteService;
use app\modules\common\services\UniversidadeService;
use app\modules\common\services\InformativoService;
use app\modules\common\services\RegulamentoService;
use app\modules\common\services\BannerService;

use app\modules\common\services\contracts\TroteServiceInterface;
use app\modules\common\services\contracts\UniversidadeServiceInterface;
use app\modules\common\services\contracts\InformativoServiceInterface;
use app\modules\common\services\contracts\RegulamentoServiceInterface;
use app\modules\common\services\contracts\BannerServiceInterface;

return [
    TroteServiceInterface::class => TroteService::class,
    UniversidadeServiceInterface::class => UniversidadeService::class,
    InformativoServiceInterface::class => InformativoService::class,
    RegulamentoServiceInterface::class => RegulamentoService::class,
    BannerServiceInterface::class => BannerService::class,
];
