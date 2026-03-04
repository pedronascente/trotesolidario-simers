<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\common\models\Helper;
?>
<style>
.certificate-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
}

.certificate-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.certificate-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.certificate-header {
    background: #28a745;
    color: white;
    padding: 15px;
    font-size: 1.1em;
    font-weight: 500;
}

.certificate-body {
    padding: 20px;
}

.certificate-link {
    display: flex;
    align-items: center;
    color: #495057;
    text-decoration: none;
    transition: color 0.3s ease;
}

.certificate-link:hover {
    color: #28a745;
    text-decoration: none;
}

.certificate-icon {
    margin-right: 10px;
    font-size: 24px;
    color: #28a745;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 20px;
    color: #dee2e6;
}

@media (max-width: 768px) {
    .certificate-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Meus Certificados</h1>
    </div>

    <!-- Certificates Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-success">Certificados Disponíveis</h6>
        </div>
        <div class="card-body">
            <?php if (empty($certificados)) : ?>
                <div class="empty-state">
                    <i class="fas fa-certificate"></i>
                    <h4>Nenhum certificado disponível</h4>
                    <p>Você ainda não possui certificados para visualizar.</p>
                </div>
            <?php else : ?>
                <div class="certificate-grid">
                    <?php foreach ($certificados as $certificado) : ?>
                        <div class="certificate-card">
                            <div class="certificate-header">
                            Trote -  <?= $certificado["trote"] ?>
                            </div>
                            <div class="certificate-body">
                                <a href="certificado/imprime?name=<?= $certificado["name"] ?>&troteid=<?= $certificado["id"] ?>&trote=<?= $certificado["trote"] ?>&tipo_doacao=<?= $certificado["tipo_doacao"] ?>" 
                                   target="_blank"
                                   class="certificate-link">
                                    <i class="fas fa-file-pdf certificate-icon"></i>
                                    <span><?= $certificado["tipo_doacao"] ?></span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona animação de fade in aos cards
    const cards = document.querySelectorAll('.certificate-card');
    cards.forEach((card, index) => {
        card.style.animation = `fadeIn 0.3s ease forwards ${index * 0.1}s`;
    });
});
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.certificate-card {
    opacity: 0;
}
</style>