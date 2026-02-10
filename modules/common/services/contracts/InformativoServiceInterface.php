<?php   

namespace app\modules\common\services\contracts;

use app\modules\common\models\Informativo;

interface InformativoServiceInterface
{

	public  function create(Informativo $informativo): bool;
	public  function update(Informativo $informativo): bool;
	public  function delete(Informativo $informativo): bool;

}