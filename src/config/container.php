<?php


use app\modules\common\services\BannerService;
use app\modules\common\services\DocumentoService;
use app\modules\common\services\TroteService;
use app\modules\common\services\UniversidadeService;
use app\modules\common\services\EventoService;
use app\modules\common\services\TipoDoacaoService;
use app\modules\common\services\DoacaoService;

use app\modules\common\services\contracts\BannerServiceInterface;
use app\modules\common\services\contracts\DocumentoServiceInterface;
use app\modules\common\services\contracts\TroteServiceInterface;
use app\modules\common\services\contracts\UniversidadeServiceInterface;
use app\modules\common\services\contracts\EventoServiceInterface;
use app\modules\common\services\contracts\TipoDoacaoServiceInterface;
use app\modules\common\services\contracts\DoacaoServiceInterface;

return [
    BannerServiceInterface::class => BannerService::class,
    DocumentoServiceInterface::class => DocumentoService::class,
    TroteServiceInterface::class => TroteService::class,
    UniversidadeServiceInterface::class => UniversidadeService::class,
    EventoServiceInterface::class => EventoService::class,
    TipoDoacaoServiceInterface::class => TipoDoacaoService::class,
    DoacaoServiceInterface::class => DoacaoService::class
];



