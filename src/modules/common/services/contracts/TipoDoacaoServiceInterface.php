<?php

namespace app\modules\common\services\contracts;

use app\modules\common\models\TipoDoacao;

interface TipoDoacaoServiceInterface
{
    public  function create(TipoDoacao $tipoDoacao): bool;
    public  function update(TipoDoacao $tipoDoacao): bool;
    public  function delete(TipoDoacao $tipoDoacao): bool;
}
