<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $fotos array<int, array{id: int, titulo: string}> */

$this->title = 'Álbum de fotos';

$this->registerCss(<<<CSS
.photo-album-card { border: 0; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(31, 45, 61, .10); height: 100%; }
.photo-album-image { width: 100%; height: 210px; object-fit: cover; display: block; background: #edf2f4; }
.photo-album-card .card-body { display: flex; flex-direction: column; gap: 14px; }
.photo-album-title { color: #243b53; font-size: 1rem; font-weight: 700; line-height: 1.35; margin: 0; }
.photo-album-download { margin-top: auto; }
.photo-album-empty { border: 1px dashed #b8c2cc; border-radius: 14px; color: #627d98; padding: 44px 24px; text-align: center; }
.photo-album-modal .modal-content { background: #101820; border: 0; }
.photo-album-modal .modal-header { border-bottom-color: rgba(255, 255, 255, .16); color: #fff; }
.photo-album-modal .close { color: #fff; opacity: .9; }
.photo-album-modal-image { display: block; height: min(68vh, 680px); margin: 0 auto; max-width: 100%; object-fit: contain; }
.photo-album-modal .carousel-control-prev, .photo-album-modal .carousel-control-next { width: 12%; }
CSS
);
?>

<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Álbum de fotos</h1>
            <p class="mb-0 text-muted">Visualize e baixe as fotos vinculadas às suas doações.</p>
        </div>
    </div>

    <?php if (empty($fotos)): ?>
        <div class="photo-album-empty">
            <i class="fas fa-images fa-2x mb-3" aria-hidden="true"></i>
            <p class="mb-0">Você ainda não possui fotos disponíveis no álbum.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($fotos as $index => $foto): ?>
                <?php
                $visualizarUrl = Url::to(['/participante/album-fotos/arquivo', 'id' => $foto['id']]);
                $downloadUrl = Url::to(['/participante/album-fotos/arquivo', 'id' => $foto['id'], 'download' => 1]);
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <article class="card photo-album-card">
                        <?= Html::a(
                            Html::img($visualizarUrl, [
                                'class' => 'photo-album-image',
                                'alt' => $foto['titulo'],
                            ]),
                            '#albumPhotosCarousel',
                            [
                                'class' => 'photo-album-preview',
                                'data-photo-index' => $index,
                                'aria-label' => 'Visualizar ' . $foto['titulo'],
                            ]
                        ) ?>
                        <div class="card-body">
                            <h2 class="photo-album-title"><?= Html::encode($foto['titulo']) ?></h2>
                            <?= Html::a(
                                '<i class="fas fa-download mr-2" aria-hidden="true"></i>Baixar foto',
                                $downloadUrl,
                                ['class' => 'btn btn-success btn-sm photo-album-download']
                            ) ?>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="modal fade photo-album-modal" id="albumPhotosModal" tabindex="-1" role="dialog" aria-labelledby="albumPhotosModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h5 mb-0" id="albumPhotosModalTitle">Visualizar fotos</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="albumPhotosCarousel" class="carousel slide" data-interval="false" data-keyboard="true">
                        <div class="carousel-inner">
                            <?php foreach ($fotos as $index => $foto): ?>
                                <?php
                                $visualizarUrl = Url::to(['/participante/album-fotos/arquivo', 'id' => $foto['id']]);
                                $downloadUrl = Url::to(['/participante/album-fotos/arquivo', 'id' => $foto['id'], 'download' => 1]);
                                ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <?= Html::img($visualizarUrl, [
                                        'class' => 'photo-album-modal-image',
                                        'alt' => $foto['titulo'],
                                    ]) ?>
                                    <div class="p-3 text-center">
                                        <p class="mb-3 text-white font-weight-bold"><?= Html::encode($foto['titulo']) ?></p>
                                        <?= Html::a(
                                            '<i class="fas fa-download mr-2" aria-hidden="true"></i>Baixar foto',
                                            $downloadUrl,
                                            ['class' => 'btn btn-success btn-sm']
                                        ) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($fotos) > 1): ?>
                            <a class="carousel-control-prev" href="#albumPhotosCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Anterior</span>
                            </a>
                            <a class="carousel-control-next" href="#albumPhotosCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Próxima</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$this->registerJs(<<<JS
$('.photo-album-preview').on('click', function (event) {
    event.preventDefault();
    $('#albumPhotosCarousel').carousel($(this).data('photo-index'));
    $('#albumPhotosModal').modal('show');
});
JS
);
?>
