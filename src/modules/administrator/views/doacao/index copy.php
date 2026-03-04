<?php

use app\modules\common\models\Helper;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\common\models\Users;
use app\modules\common\models\Universidade;
use app\modules\common\models\Trote;

?>
<?php $this->registerCssFile('@web/css/styles.css'); ?>
<!-- Lightbox CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">

<style>

</style>
<div class="container-fluid">
    <div class="page-header">
        <div class="header-content p-3">
            <div>
                <h1 class="mb-2">Doações</h1>
            </div>
            <div class="header-actions">
                <?= Html::a('<i class="fas fa-plus"></i> Nova Doação', ['create'], ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-upload"></i> Importar', ['import'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
    <div class="filters-section">
        <h5 class="mb-3"><i class="fas fa-filter"></i> Filtros</h5>
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Usuário</label>
                <select class="form-control search-input" id="filter-user">
                    <option value="">Todos os usuários</option>
                    <?php foreach (Users::find()->where(['status' => '1'])->all() as $user): ?>
                        <option value="<?= $user->id ?>"><?= $user->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Instituição</label>
                <select class="form-control search-input" id="filter-institution">
                    <option value="">Todas as instituições</option>
                    <?php foreach (Universidade::find()->all() as $uni): ?>
                        <option value="<?= $uni->id ?>"><?= $uni->nome ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Tipo de Doação</label>
                <select class="form-control search-input" id="filter-type">
                    <option value="">Todos os tipos</option>
                    <option value="Alimentos">Alimentos</option>
                    <option value="Comissão">Comissão</option>
                    <option value="Participação Presencial">Participação Presencial</option>
                    <option value="Sangue">Sangue</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select class="form-control search-input" id="filter-status">
                    <option value="">Todos os status</option>
                    <option value="1">Aprovado</option>
                    <option value="0">Rejeitado</option>
                    <option value="2">Pendente</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="donations-container">
        <?php foreach ($dataProvider->getModels() as $model):
            $user = Users::find()->where(['id' => $model->user_create])->one();
            $universidade = Universidade::find()->where(['id' => $model->instituicao])->one();
            $trote = $model->trote;
            $statusClass = '';
            $statusText = '';
            if ($model->validado === 1) {
                $statusClass = 'status-approved';
                $statusText = 'Aprovado';
            } elseif ($model->validado === 0) {
                $statusClass = 'status-rejected';
                $statusText = 'Rejeitado';
            } else {
                $statusClass = 'status-pending';
                $statusText = 'Pendente';
            }
            $typeClass = 'type-' . strtolower(str_replace([' ', 'ã'], ['', 'a'], $model->tipo_doacao));
            ?>
            <div class="col-lg-6 col-xl-4">
                <div class="donation-card" data-user="<?= $model->user_create ?>"
                    data-institution="<?= $model->instituicao ?>" data-type="<?= $model->tipo_doacao ?>"
                    data-status="<?= $model->validado ?>">
                    <?php if ($model->arquivo): ?>
                        <div class="donation-image">
                            <img src="/imagens/doacoes/<?= $model->arquivo ?>" alt="Doação">
                            <div class="donation-overlay">
                                <a href="/imagens/doacoes/<?= $model->arquivo ?>" data-lightbox="donation-<?= $model->id ?>"
                                    data-title="Doação de <?= $user ? $user->name : 'Usuário' ?>">
                                    <i class="fas fa-search-plus zoom-icon"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="donation-content">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="donation-user">
                                <div class="user-info">
                                    <h5><?= $user ? $user->name : 'Usuário não encontrado' ?></h5>
                                    <small><?= $user ? $user->email : '' ?></small>
                                </div>
                            </div>
                            <span class="donation-type ">
                                Tipo: <?= $model->tipo_doacao ?>
                            </span>
                        </div>
                        <div class="donation-details">
                            <div class="detail-row">
                                <span class="detail-label">Instituição:</span>
                                <span class="detail-value"><?= $universidade ? $universidade->nome : 'N/A' ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Trote:</span>
                                <span class="detail-value"><?= $trote ? $trote->nome : 'N/A' ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="filter-label mb-2">Motivo/Observações:</label>
                            <textarea class="motivo-textarea" placeholder="Adicione observações sobre esta doação..."
                                onblur="salvaMotivo('<?= $model->id ?>',this.value)"><?= $model->validado_motivo ?></textarea>
                        </div>
                        <div class="donation-actions" id="doacao-div-<?= $model->id ?>">
                            <?php if ($model->validado === 1): ?>
                                <button class="action-btn btn-reject" onclick="validaDoacao(<?= $model->id ?>)">
                                    <i class="fas fa-times"></i> Rejeitar
                                </button>
                            <?php else: ?>
                                <button class="action-btn btn-approve" onclick="validaDoacao(<?= $model->id ?>)">
                                    <i class="fas fa-check"></i> Aprovar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="d-flex justify-content-center mt-4">
        <?= \yii\widgets\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'pagination pagination-lg'],
            'linkOptions' => ['class' => 'page-link'],
            'pageCssClass' => 'page-item',
            'prevPageCssClass' => 'page-item',
            'nextPageCssClass' => 'page-item',
            'firstPageCssClass' => 'page-item',
            'lastPageCssClass' => 'page-item',
        ]); ?>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

<script>
    $(document).ready(function () {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': "Imagem %1 de %2"
        });
        window.validaDoacao = function (id) {
            console.log(id);
            const container = $('button[onclick*="' + id + '"]').parent();
            const originalContent = container.html();
            container.html('<span class="spinner-border spinner-border-sm" role="status"></span> Processando...');

            $.ajax({
                url: '/administrator/doacao/check',
                data: {
                    "model_id": id
                },
                type: "POST",
                dataType: 'json',
                success: function (result) {
                    let buttonHtml = '';
                    let statusClass = '';
                    let statusText = '';

                    if (result[0].includes('check-square')) {
                        buttonHtml = '<button class="action-btn btn-reject" onclick="validaDoacao(' + id + ')"><i class="fas fa-times"></i> Rejeitar</button>';
                        statusClass = 'status-approved';
                        statusText = 'Aprovado';
                    } else {
                        buttonHtml = '<button class="action-btn btn-approve" onclick="validaDoacao(' + id + ')"><i class="fas fa-check"></i> Aprovar</button>';
                        statusClass = 'status-pending';
                        statusText = 'Pendente';
                    }

                    container.html(buttonHtml);

                    const card = container.closest('.donation-card');
                    const statusBadge = card.find('.status-badge');
                    statusBadge.removeClass('status-approved status-rejected status-pending').addClass(statusClass).text(statusText);

                    card.attr('data-status', result[0].includes('check-square') ? '1' : '2');

                },
                error: function (xhr, status, error) {
                    console.error('Erro na requisição:', error);
                    container.html(originalContent);
                }
            });
        }

        function salvaMotivo(id, texto) {
            $.ajax({
                url: '/administrator/doacao/atualizamotivo',
                data: {
                    "model_id": id,
                    "texto": texto
                },
                type: "POST",
                dataType: 'json',
                success: function (result) {
                    showNotification('Motivo salvo com sucesso!', 'success');
                },
                error: function () {
                    showNotification('Erro ao salvar motivo. Contate a TI.', 'error');
                }
            });
        }

        $('#filter-user, #filter-institution, #filter-type, #filter-status').on('change', function () {
            filterDonations();
        });

        function filterDonations() {
            const userFilter = $('#filter-user').val();
            const institutionFilter = $('#filter-institution').val();
            const typeFilter = $('#filter-type').val();
            const statusFilter = $('#filter-status').val();

            $('.donation-card').each(function () {
                const card = $(this).parent();
                const userData = $(this).data('user');
                const institutionData = $(this).data('institution');
                const typeData = $(this).data('type');
                const statusData = $(this).data('status');

                let show = true;

                if (userFilter && userData != userFilter) show = false;
                if (institutionFilter && institutionData != institutionFilter) show = false;
                if (typeFilter && typeData != typeFilter) show = false;
                if (statusFilter && statusData != statusFilter) show = false;

                if (show) {
                    card.show();
                } else {
                    card.hide();
                }
            });
        }

        function showNotification(message, type = 'info') {
            const notification = $(
            <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" 
                style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        );

            $('body').append(notification);

            setTimeout(() => {
                notification.alert('close');
            }, 3000);
        }
    });
</script>