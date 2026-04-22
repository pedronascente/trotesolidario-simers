<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Minha area';

$totalParticipacoes = count($participacoes);
$totalParticipacoesAtivas = count($participacoesAtivas);
$totalDoacoes = count($doacoes);
$totalCertificados = count($certificados);
?>

<style>
    .food-donation-card {
        border: 0;
        border-radius: 24px;
        overflow: hidden;
        background: linear-gradient(135deg, #f7fbff 0%, #ffffff 48%, #f2fbf7 100%);
        box-shadow: 0 18px 45px rgba(28, 56, 86, 0.08);
    }

    .food-donation-hero {
        position: relative;
        padding: 32px;
        background:
            radial-gradient(circle at top right, rgba(47, 198, 142, 0.18), transparent 32%),
            radial-gradient(circle at left bottom, rgba(53, 126, 221, 0.14), transparent 36%),
            linear-gradient(135deg, #ffffff 0%, #f8fcff 45%, #f4fcf7 100%);
        border-bottom: 1px solid rgba(21, 80, 135, 0.08);
    }

    .food-donation-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #1d65d6;
        background: rgba(29, 101, 214, 0.10);
        margin-bottom: 14px;
    }

    .food-donation-title {
        margin: 0;
        font-size: 2rem;
        line-height: 1.15;
        color: #26334d;
    }

    .food-donation-copy {
        margin-top: 14px;
        max-width: 980px;
        font-size: 1.05rem;
        line-height: 1.7;
        color: #6a7488;
    }

    .food-donation-subtitle {
        margin: 26px 0 0;
        font-size: 1.9rem;
        font-weight: 300;
        text-align: center;
        color: #7a8195;
    }

    .food-donation-grid {
        padding: 28px 24px 32px;
    }

    .food-donation-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 92px;
        padding: 18px 20px;
        border-radius: 16px;
        text-align: center;
        text-decoration: none;
        color: #ffffff;
        background: linear-gradient(135deg, #2dcb93 0%, #21ba84 100%);
        box-shadow: 0 14px 24px rgba(33, 186, 132, 0.20);
        transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
    }

    .food-donation-link:hover,
    .food-donation-link:focus {
        color: #ffffff;
        text-decoration: none;
        transform: translateY(-3px);
        box-shadow: 0 18px 28px rgba(33, 186, 132, 0.28);
        filter: saturate(1.03);
    }

    .food-donation-link span {
        font-size: 1.05rem;
        font-weight: 600;
        line-height: 1.35;
    }

    @media (max-width: 991.98px) {
        .food-donation-hero {
            padding: 24px;
        }

        .food-donation-title {
            font-size: 1.7rem;
        }

        .food-donation-subtitle {
            font-size: 1.55rem;
        }
    }

    @media (max-width: 575.98px) {
        .food-donation-grid {
            padding: 20px 16px 24px;
        }

        .food-donation-link {
            min-height: 80px;
            border-radius: 14px;
        }

        .food-donation-link span {
            font-size: 1rem;
        }
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Minha area</h2>
            <span class="text-muted">Acompanhe sua participacao no Trote Solidario</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <?= Html::a('Meu perfil', ['/participante/users/perfil'], ['class' => 'btn btn-outline-dark']) ?>
            <?= Html::a('Nova doacao', ['/participante/doacao/create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php if (!empty($trotesDisponiveis)): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-8 col-lg-6">
                        <label class="small text-muted text-uppercase mb-2">Trote exibido na home</label>
                        <form method="get" action="<?= Html::encode(Url::to(['/participante/default/home'])) ?>">
                            <select name="trote_id" class="form-control" onchange="this.form.submit()">
                                <option value="">Selecione um trote ativo</option>
                                <?php foreach ($trotesDisponiveis as $troteOption): ?>
                                    <option value="<?= (int) $troteOption->id ?>" <?= $selectedTroteId === (int) $troteOption->id ? 'selected' : '' ?>>
                                        <?= Html::encode(($troteOption->titulo ?: 'Trote') . ' | ' . ($troteOption->edicao ?? '-')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-4 col-lg-6 mt-3 mt-md-0 text-md-right text-muted small">
                        <?php if ($selectedTroteId === null && count($trotesDisponiveis) > 1): ?>
                            Escolha o trote para ver ranking, doacoes e certificados do contexto correto.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($banner !== null && (!empty($banner->img_dsk) || !empty($banner->img_mob))): ?>
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <img src="/img/<?= Html::encode(\app\modules\common\models\Helper::isMobile() ? ($banner->img_mob ?: $banner->img_dsk) : ($banner->img_dsk ?: $banner->img_mob)) ?>" style="width:100%;height:auto;" alt="Banner do trote">
        </div>
    <?php endif; ?>

    <?php if ($troteAtivo !== null): ?>
        <div class="card shadow-sm border-0 mb-4 border-start border-4 border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Trote ativo</h5>
                    <span class="badge bg-success"><?= Html::encode($troteAtivo->getStatusLabel()) ?></span>
                </div>
                <h4 class="fw-bold mb-1"><?= Html::encode($troteAtivo->titulo ?: ('Trote ' . $troteAtivo->edicao)) ?></h4>
                <div class="text-muted">Edicao <?= Html::encode($troteAtivo->edicao) ?></div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <small class="text-muted">Inicio</small>
                        <div class="fw-semibold"><?= $troteAtivo->data_inicio ? Yii::$app->formatter->asDate($troteAtivo->data_inicio, 'php:d/m/Y') : '-' ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Fim</small>
                        <div class="fw-semibold"><?= $troteAtivo->data_fim ? Yii::$app->formatter->asDate($troteAtivo->data_fim, 'php:d/m/Y') : '-' ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Minhas participacoes ativas</small>
                        <div class="fw-semibold"><?= $totalParticipacoesAtivas ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Meu ranking universitario</small>
                        <div class="fw-semibold"><?= !empty($ranking) ? 'Atualizado' : 'Sem dados' ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ([
            ['label' => 'Participacoes', 'value' => $totalParticipacoes, 'color' => 'primary'],
            ['label' => 'Participacoes ativas', 'value' => $totalParticipacoesAtivas, 'color' => 'success'],
            ['label' => 'Doacoes', 'value' => $totalDoacoes, 'color' => 'warning'],
            ['label' => 'Certificados', 'value' => $totalCertificados, 'color' => 'info'],
        ] as $card): ?>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase mb-1"><?= Html::encode($card['label']) ?></div>
                        <div class="display-6 fw-bold text-<?= Html::encode($card['color']) ?>"><?= (int) $card['value'] ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Minhas participacoes</h5>
                        <?= Html::a('Ver doacoes', ['/participante/doacao'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>

                    <?php if (!empty($participacoes)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($participacoes as $participacao): ?>
                                <?php
                                    $trote = $participacao->trote;
                                    $troteLabel = $trote !== null
                                        ? (($trote->titulo ?: 'Trote') . ' | ' . ($trote->edicao ?? '-'))
                                        : 'Trote nao encontrado';
                                ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold"><?= Html::encode($participacao->universidade->nome ?? '-') ?></div>
                                        <small class="text-muted"><?= Html::encode($troteLabel) ?></small>
                                    </div>
                                    <span class="badge <?= $participacao->status === \app\modules\common\models\Participacao::STATUS_ATIVO ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= Html::encode(\app\modules\common\models\Participacao::getStatusList()[$participacao->status] ?? $participacao->status) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">Voce ainda nao possui participacoes cadastradas.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Ranking por universidade</h5>
                        <?= Html::a('Ranking completo', ['/participante/default/ranking', 'trote_id' => $selectedTroteId], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>

                    <?php if ($selectedTroteId === null && !empty($trotesDisponiveis) && count($trotesDisponiveis) > 1): ?>
                        <div class="text-muted">Selecione um trote acima para visualizar o ranking correspondente.</div>
                    <?php elseif (!empty($ranking)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Universidade</th>
                                        <th>Pontos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ranking as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= Html::encode($item['nome']) ?></td>
                                            <td><strong><?= (int) $item['pontos'] ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">O ranking ainda nao possui dados suficientes para exibicao.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Informativos</h5>
                    <?php if (!empty($informativos)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($informativos as $informativo): ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span><?= Html::encode($informativo->nome ?? ('Documento #' . $informativo->id)) ?></span>
                                    <a href="/pdf/<?= Html::encode($informativo->arquivo) ?>" target="_blank" class="btn btn-outline-success btn-sm">Abrir</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">Nenhum informativo disponivel no momento.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Regulamentos</h5>
                    <?php if (!empty($regulamentos)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($regulamentos as $regulamento): ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span><?= Html::encode($regulamento->nome ?? ('Regulamento #' . $regulamento->id)) ?></span>
                                    <a href="/pdf/<?= Html::encode($regulamento->arquivo) ?>" target="_blank" class="btn btn-outline-success btn-sm">Abrir</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">Nenhum regulamento disponivel no momento.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($universidadesDoacao)): ?>
        <div class="food-donation-card mt-5 mb-4">
            <div class="food-donation-hero">
                <div class="food-donation-badge">Informativo Doacao de Alimentos</div>
                <h3 class="food-donation-title">Doe alimentos de forma simples, online e com destino local.</h3>
                <p class="food-donation-copy">
                    A arrecadacao de donativos e realizada de forma online por meio de links personalizados para cada universidade, direcionados ao Banco de Alimentos do Estado.
                    Escolha a instituicao que deseja apoiar, selecione os itens e a quantidade, e conclua a doacao como em uma compra online. Ao final da acao,
                    todo o valor arrecadado sera destinado a instituicoes carentes da cidade sede da universidade escolhida.
                </p>
                <p class="food-donation-subtitle">Clique e escolha a universidade para doar alimentos</p>
            </div>

            <div class="food-donation-grid">
                <div class="row">
                    <?php foreach ($universidadesDoacao as $universidade): ?>
                        <div class="col-sm-6 col-lg-4 col-xl-3 mb-3">
                            <?= Html::a(
                                '<span>' . Html::encode($universidade->nome) . '</span>',
                                $universidade->link_doacao_alimento,
                                [
                                    'class' => 'food-donation-link',
                                    'target' => '_blank',
                                    'rel' => 'noopener noreferrer',
                                    'aria-label' => 'Doar alimentos para ' . $universidade->nome,
                                ]
                            ) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
