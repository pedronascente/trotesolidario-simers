<?php

namespace tests\unit\modules\common\models;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/UniversityCorrectionReviewForm.php';

use app\modules\common\models\UniversityCorrectionReviewForm;
use PHPUnit\Framework\TestCase;

class UniversityCorrectionReviewFormTest extends TestCase
{
    public function testApprovalDoesNotRequireReviewNotes(): void
    {
        $model = new UniversityCorrectionReviewForm();
        $model->decision = UniversityCorrectionReviewForm::DECISION_APPROVE;

        $this->assertTrue($model->validate());
    }

    public function testRejectionRequiresReviewNotes(): void
    {
        $model = new UniversityCorrectionReviewForm();
        $model->decision = UniversityCorrectionReviewForm::DECISION_REJECT;
        $model->review_notes = '   ';

        $this->assertFalse($model->validate());
        $this->assertSame('Informe o motivo da reprovacao.', $model->getFirstError('review_notes'));
    }

    public function testRejectionAcceptsAndTrimsReviewNotes(): void
    {
        $model = new UniversityCorrectionReviewForm();
        $model->decision = UniversityCorrectionReviewForm::DECISION_REJECT;
        $model->review_notes = '  Documento insuficiente.  ';

        $this->assertTrue($model->validate());
        $this->assertSame('Documento insuficiente.', $model->review_notes);
    }

    public function testInvalidDecisionIsRejected(): void
    {
        $model = new UniversityCorrectionReviewForm();
        $model->decision = 'invalid';

        $this->assertFalse($model->validate());
    }
}
