<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\participante\models\Helper;
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Certificados</h1>
    </div>
    <!-- Color System -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                </div>
                <div class="p-3">
                    <ul>
                        <?php foreach ($certificados as $certificado) : ?>
                            <li><a href="certificado/imprime?name=<?= $certificado["name"] ?>&troteid=<?= $certificado["id"] ?>&trote=<?= $certificado["trote"] ?>&tipo_doacao=<?= $certificado["tipo_doacao"] ?>" target="_blank"><?= $certificado["trote"] . " - " .$certificado["tipo_doacao"] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>