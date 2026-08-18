<?php

use app\modules\common\models\Doacao;
use yii\helpers\Html;

$this->title = 'Meus certificados';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Voltar para home', ['/participante/default/home'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
        Cada certificado representa a sua participacao em uma edicao do trote. Quando novas doacoes aprovadas entram na mesma participacao, 
        o certificado existente e atualizado com a nova carga horaria, em vez de criar um novo item na lista.
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">Certificados disponiveis</h6>
        </div>
        <div class="card-body">
            <?php if (empty($certificados)): ?>
                <div class="text-muted text-center py-4">
                    Nenhum certificado disponivel no momento.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($certificados as $certificado): ?>                       
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge bg-success" style="color: #fff;">Disponivel</span>
                                    </div>
                                    <div class="mb-2"><strong>Trote:</strong> <?= Html::encode($certificado->participacao->trote->titulo) ?></div>
                                    <div class="mb-2"><strong>Edição:</strong> <?= Html::encode($certificado->participacao->trote->edicao ?? '-') ?></div>
                                    <div class="mb-2"><strong>Universidade:</strong> <?= Html::encode($certificado->participacao->universidade->nome ?? '-') ?></div>
                                    <div class="mb-2">
                                        <strong>Tipos de doações:</strong><br>
                                        <?php 
                                            $tipos = [];
                                            if (!empty($certificado->participacao->doacoes)) {
                                                foreach ($certificado->participacao->doacoes as $doacao) {
                                                    if ($doacao->status === Doacao::STATUS_APROVADA && $doacao->tipoDoacao) {
                                                        $tipos[] = $doacao->tipoDoacao->nome;
                                                    }
                                                }
                                            }
                                            $tipos = array_unique($tipos); // evita repetição
                                        ?>
                                        <?php if (!empty($tipos)): ?>
                                            <?php foreach ($tipos as $tipo): ?>
                                                <span class="badge bg-primary me-1"  style="color: #fff;"   >
                                                    <?= Html::encode($tipo) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Nenhuma doação registrada</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-1"><strong>Ultima atualizacao:</strong> <?= Yii::$app->formatter->asDatetime($certificado->data_emissao, 'php:d/m/Y H:i') ?></div>
                                    <div class="text-muted small mb-3">Este horario muda quando novas doacoes aprovadas atualizam o certificado da mesma participacao.</div>
                                    <div class="mt-auto">
                                        <?= Html::a('Visualizar certificado', ['imprime', 'id' => $certificado->id], ['class' => 'btn btn-success w-100', 'target' => '_blank', 'rel' => 'noopener']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
