<?php   

namespace app\modules\common\services\contracts;

use app\modules\common\models\Trote;

interface TroteServiceInterface
{
	public  function create(Trote $trote): bool;
	public  function update(Trote $trote): bool;
	public  function delete(Trote $trote): void;
}


