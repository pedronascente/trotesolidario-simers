<?php   

namespace app\modules\common\services\contracts;

use app\modules\common\models\Regulamento;


interface RegulamentoServiceInterface
{

	public  function create(Regulamento $regulamento): bool;
	public  function update(Regulamento $regulamento): bool;
	public  function delete(Regulamento $regulamento): bool;

}