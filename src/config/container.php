<?php

use app\modules\common\services\BannerService;
use app\modules\common\services\DocumentoService;
use app\modules\common\services\TroteService;
use app\modules\common\services\UniversidadeService;
use app\modules\common\services\EventoService;
use app\modules\common\services\TipoDoacaoService;
use app\modules\common\services\DoacaoService;
use app\modules\common\services\UserService;
use app\modules\common\services\ParticipanteService;
use app\modules\common\services\ParticipacaoService;
use app\modules\common\services\RankingCacheService;
use app\modules\common\services\CertificadoService;
use app\modules\common\services\GestaoCustosService;
use app\services\auth\AuthService;

use app\modules\common\services\contracts\BannerServiceInterface;
use app\modules\common\services\contracts\DocumentoServiceInterface;
use app\modules\common\services\contracts\TroteServiceInterface;
use app\modules\common\services\contracts\UniversidadeServiceInterface;
use app\modules\common\services\contracts\EventoServiceInterface;
use app\modules\common\services\contracts\TipoDoacaoServiceInterface;
use app\modules\common\services\contracts\DoacaoServiceInterface;
use app\modules\common\services\contracts\UserServiceInterface;
use app\modules\common\services\contracts\ParticipanteServiceInterface;
use app\modules\common\services\contracts\ParticipacaoServiceInterface;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use app\modules\common\services\contracts\GestaoCustosServiceInterface;
use app\services\auth\AuthServiceInterface;

return [
    BannerServiceInterface::class => BannerService::class,
    DocumentoServiceInterface::class => DocumentoService::class,
    TroteServiceInterface::class => TroteService::class,
    UniversidadeServiceInterface::class => UniversidadeService::class,
    EventoServiceInterface::class => EventoService::class,
    TipoDoacaoServiceInterface::class => TipoDoacaoService::class,
    DoacaoServiceInterface::class => DoacaoService::class,
    UserServiceInterface::class => UserService::class,
    ParticipanteServiceInterface::class => ParticipanteService::class,
    ParticipacaoServiceInterface::class => ParticipacaoService::class,
    RankingCacheServiceInterface::class => RankingCacheService::class,
    CertificadoServiceInterface::class => CertificadoService::class,
    GestaoCustosServiceInterface::class => GestaoCustosService::class,
    AuthServiceInterface::class => AuthService::class,
];
