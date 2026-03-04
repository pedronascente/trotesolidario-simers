<?php   

namespace app\modules\common\services\contracts;

use app\modules\common\models\Documento;

interface DocumentoServiceInterface
{

	public  function create(Documento $documento): bool;
	public  function update(Documento $documento): bool;
	public  function delete(Documento $documento): bool;

}