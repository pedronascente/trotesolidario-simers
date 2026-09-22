<?php
use app\modules\common\models\Helper;
use app\modules\common\models\Banner;
use yii\helpers\Html;
use yii\helpers\Url;

$troteAtivoEdition = $troteAtivo !== null
    ? str_replace('.', '/', (string) $troteAtivo->edicao)
    : null;
$troteDisplayName = $troteAtivoEdition !== null && $troteAtivoEdition !== ''
    ? 'Trote ' . $troteAtivoEdition
    : 'Trote Solidário';

// Estilos customizados para manter o escopo limpo
$this->registerCss("
    .trote-container { font-family: 'Poppins', sans-serif; color: #1f1d44; }
    .img-banner { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
    .img-banner:hover { transform: scale(1.01); }

    .hero-title { font-weight: 800; color: #1f1d44; letter-spacing: -1px; margin-top: 2rem; }
    .hero-subtitle { font-size: 1.1rem; color: #666; max-width: 600px; margin: 0 auto 2rem; }

    .btn-custom { border-radius: 50px; padding: 12px 30px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .btn-custom:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
    .btn-custom:disabled { transform: none; box-shadow: none; cursor: not-allowed; opacity: 0.65; }

    .instruction-card { background: linear-gradient(135deg, #00b38d 0%, #00d4a7 100%); border-radius: 20px; padding: 30px; color: #fff; margin: 40px 0; border: none; }

    .uni-card {
        background: #1f1d44; color: #fff; border-radius: 12px; padding: 20px;
        display: flex; align-items: center; justify-content: center; text-align: center;
        min-height: 100px; transition: all 0.3s; border: 2px solid transparent; text-decoration: none !important;
    }
    .uni-card:hover { background: #2a285c; border-color: #00b38d; transform: translateY(-5px); color: #fff; }

    .participar-modal-backdrop {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(17, 24, 39, 0.55);
        z-index: 1050;
    }
    .participar-modal-backdrop.is-open { display: flex; }
    .participar-modal-card {
        width: 100%;
        max-width: 480px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        overflow: hidden;
    }
    .participar-modal-header {
        padding: 24px 24px 12px;
        background: linear-gradient(135deg, #1f1d44 0%, #2d2a66 100%);
        color: #ffffff;
    }
    .participar-modal-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
    }
    .participar-modal-body {
        padding: 24px;
    }
    .participar-modal-copy {
        margin-bottom: 18px;
        color: #5f6677;
        line-height: 1.6;
    }
    .participar-modal-close {
        border: 0;
        background: transparent;
        color: #ffffff;
        font-size: 1.5rem;
        line-height: 1;
        padding: 0;
        cursor: pointer;
    }
    .participar-modal-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        color: #1f1d44;
    }
    .participar-modal-input {
        width: 100%;
        height: 48px;
        border: 1px solid #d6d9e0;
        border-radius: 12px;
        padding: 0 16px;
        font-size: 1rem;
        color: #1f1d44;
    }
    .participar-modal-input:focus {
        outline: none;
        border-color: #00b38d;
        box-shadow: 0 0 0 4px rgba(0, 179, 141, 0.12);
    }
    .participar-modal-error {
        display: none;
        margin-top: 8px;
        color: #c0392b;
        font-size: 0.9rem;
    }
    .participar-modal-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 22px;
    }
    @media (max-width: 575.98px) {
        .participar-modal-header,
        .participar-modal-body {
            padding: 20px;
        }
    }
");
?>

<div class="container trote-container pb-5">
    <div class="row">
        <div class="col-lg-10 mx-auto text-center">

            <div class="py-4">
                <?php if ($capa && ($capa->img_dsk || $capa->img_mob) && $capa->ativo == 1 && $capa->tipo === Banner::TIPO_LOGIN): ?>
                    <?php $bannerImage = Helper::isMobile() ? ($capa->img_mob ?: $capa->img_dsk) : ($capa->img_dsk ?: $capa->img_mob); ?>
                    <?= Html::img('/img/' . rawurlencode(basename($bannerImage)), [
                        'class' => 'img-fluid img-banner',
                        'alt' => 'Trote Solidário 2026'
                    ]) ?>
                <?php endif; ?>
            </div>

            <h1 class="hero-title mt-4">Participe do <?= Html::encode($troteDisplayName) ?></h1>
            <p class="hero-subtitle">
                O maior movimento de solidariedade acadêmica do RS precisa da sua energia.
                Sua recepção aos novos colegas pode transformar vidas através da doação.
            </p>

            <div class="d-flex justify-content-center flex-wrap mb-5">
                <?= Html::button('Participar agora', [
                    'class' => 'btn btn-success btn-custom m-2',
                    'type' => 'button',
                    'id' => 'participar-agora-trigger',
                    'disabled' => $troteAtivo === null,
                    'aria-disabled' => $troteAtivo === null ? 'true' : 'false',
                    'title' => $troteAtivo === null ? 'Nenhum trote ativo disponível no momento.' : null,
                ]) ?>
                <?= Html::a('Área do participante', ['/auth/login'], ['class' => 'btn btn-primary btn-custom m-2']) ?>
            </div>

            <div
                id="participar-modal"
                class="participar-modal-backdrop"
                role="dialog"
                aria-modal="true"
                aria-labelledby="participar-modal-title"
                aria-hidden="true"
            >
                <div class="participar-modal-card">
                    <div class="participar-modal-header d-flex align-items-start justify-content-between">
                        <div>
                            <h3 id="participar-modal-title">Participar do <?= Html::encode($troteDisplayName) ?></h3>
                        </div>
                        <button type="button" class="participar-modal-close" id="participar-modal-close" aria-label="Fechar modal">&times;</button>
                    </div>
                    <div class="participar-modal-body text-left">
                        <p class="participar-modal-copy">
                            Informe seu CPF para participar do <?= Html::encode($troteDisplayName) ?>.
                        </p>

                        <form id="participar-modal-form" action="<?= Html::encode(Url::to(['/participante/default/start-participation'])) ?>" method="post" novalidate>
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                            <label for="participar-cpf" class="participar-modal-label">CPF</label>
                            <input
                                type="text"
                                id="participar-cpf"
                                name="cpf"
                                class="participar-modal-input"
                                inputmode="numeric"
                                maxlength="14"
                                placeholder="000.000.000-00"
                                autocomplete="off"
                            >
                            <div id="participar-cpf-error" class="participar-modal-error">
                                Informe um CPF valido para continuar.
                            </div>
                            <div id="participar-cpf-feedback" class="participar-modal-error"></div>

                            <div class="participar-modal-actions">
                                <?= Html::submitButton('Continuar', ['class' => 'btn btn-success btn-custom px-4']) ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="instruction-card text-left shadow-lg">
                <div class="row align-items-center">
                    <div class="col-md-1 text-center d-none d-md-block">
                        <i class="fas fa-university fa-2x"></i>
                    </div>
                    <div class="col-md-11">
                        <h4 class="mb-2" style="font-weight: 800;">Como contribuir?</h4>
                        <p class="mb-0">Selecione sua <strong>Universidade</strong> abaixo para ser redirecionado ao portal de doação de alimentos. Sua ação transforma o trote em solidariedade real.</p>
                    </div>
                </div>
            </div>
                    <br>
            <div class="row">
                <?php foreach ($universidades_botoes as $universidade) : ?>
                    <div class="col-md-4 mb-4">
                        <a href="<?= $universidade->link_doacao_alimento ?>" class="uni-card shadow-sm" target="_blank">
                            <span class="font-weight-bold"><?= $universidade->nome ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-5 pt-3">
                <p class="text-muted italic">"Sua ação não é trote, é solidariedade de verdade."</p>
            </div>

        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
(function () {
    var modal = document.getElementById('participar-modal');
    var modalCard = modal ? modal.querySelector('.participar-modal-card') : null;
    var trigger = document.getElementById('participar-agora-trigger');
    var closeButton = document.getElementById('participar-modal-close');
    var form = document.getElementById('participar-modal-form');
    var cpfInput = document.getElementById('participar-cpf');
    var error = document.getElementById('participar-cpf-error');
    var feedback = document.getElementById('participar-cpf-feedback');
    var previousBodyOverflow = '';
    var lastFocusedElement = null;

    if (!modal || !modalCard || !trigger || !closeButton || !form || !cpfInput || !error || !feedback) {
        return;
    }

    function getFocusableElements() {
        return modalCard.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    }

    function openModal() {
        lastFocusedElement = document.activeElement;
        previousBodyOverflow = document.body.style.overflow;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        window.setTimeout(function () {
            cpfInput.focus();
        }, 50);
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = previousBodyOverflow;
        error.style.display = 'none';
        feedback.style.display = 'none';
        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        }
    }

    function showFieldError(message) {
        error.textContent = message;
        error.style.display = 'block';
        feedback.style.display = 'none';
    }

    function showFeedback(message) {
        feedback.textContent = message;
        feedback.style.display = 'block';
        error.style.display = 'none';
    }

    function applyCpfMask(value) {
        var digits = value.replace(/\\D/g, '').slice(0, 11);

        if (digits.length > 9) {
            return digits.replace(/(\\d{3})(\\d{3})(\\d{3})(\\d{1,2})/, '$1.$2.$3-$4');
        }

        if (digits.length > 6) {
            return digits.replace(/(\\d{3})(\\d{3})(\\d{1,3})/, '$1.$2.$3');
        }

        if (digits.length > 3) {
            return digits.replace(/(\\d{3})(\\d{1,3})/, '$1.$2');
        }

        return digits;
    }

    function isValidCpf(value) {
        var cpf = value.replace(/\\D/g, '');
        var sum = 0;
        var remainder;
        var i;

        if (cpf.length !== 11 || /^(\\d)\\1{10}$/.test(cpf)) {
            return false;
        }

        for (i = 1; i <= 9; i++) {
            sum += parseInt(cpf.substring(i - 1, i), 10) * (11 - i);
        }

        remainder = (sum * 10) % 11;
        if (remainder === 10 || remainder === 11) {
            remainder = 0;
        }

        if (remainder !== parseInt(cpf.substring(9, 10), 10)) {
            return false;
        }

        sum = 0;
        for (i = 1; i <= 10; i++) {
            sum += parseInt(cpf.substring(i - 1, i), 10) * (12 - i);
        }

        remainder = (sum * 10) % 11;
        if (remainder === 10 || remainder === 11) {
            remainder = 0;
        }

        return remainder === parseInt(cpf.substring(10, 11), 10);
    }

    trigger.addEventListener('click', openModal);
    closeButton.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (!modal.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'Escape') {
            closeModal();
            return;
        }

        if (event.key === 'Tab') {
            var focusableElements = getFocusableElements();
            if (!focusableElements.length) {
                return;
            }

            var firstElement = focusableElements[0];
            var lastElement = focusableElements[focusableElements.length - 1];

            if (event.shiftKey && document.activeElement === firstElement) {
                event.preventDefault();
                lastElement.focus();
                return;
            }

            if (!event.shiftKey && document.activeElement === lastElement) {
                event.preventDefault();
                firstElement.focus();
            }
        }
    });

    cpfInput.addEventListener('input', function () {
        cpfInput.value = applyCpfMask(cpfInput.value);
        if (cpfInput.value.replace(/\\D/g, '').length === 11) {
            error.style.display = 'none';
        }
        feedback.style.display = 'none';
    });

    form.addEventListener('submit', function (event) {
        var formData;

        event.preventDefault();

        if (cpfInput.value.replace(/\\D/g, '').length === 0) {
            showFieldError('Informe o CPF para continuar.');
            cpfInput.focus();
            return;
        }

        if (!isValidCpf(cpfInput.value)) {
            showFieldError('CPF invalido. Verifique o numero informado e tente novamente.');
            cpfInput.focus();
            return;
        }

        formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData,
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data || data.success !== true || !data.redirectUrl) {
                    showFeedback((data && data.message) || 'Nao foi possivel validar o CPF neste momento. Tente novamente em instantes.');
                    return;
                }

                window.location.href = data.redirectUrl;
            })
            .catch(function () {
                showFeedback('Nao foi possivel validar o CPF neste momento. Tente novamente em instantes.');
            });
    });
})();
JS);
?>
