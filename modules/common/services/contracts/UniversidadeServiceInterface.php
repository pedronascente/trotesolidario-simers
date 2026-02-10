<?php   

namespace app\modules\common\services\contracts;

use app\modules\common\models\Universidade;


interface UniversidadeServiceInterface
{

	public  function create(Universidade $universidade): bool;
	public  function update(Universidade $universidade): bool;
	public  function toggleAtivo(Universidade $universidade): bool;

}