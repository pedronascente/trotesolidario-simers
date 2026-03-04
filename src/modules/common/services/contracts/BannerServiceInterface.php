<?php   

namespace app\modules\common\services\contracts;

use app\modules\common\models\Banner;

interface BannerServiceInterface
{
	public function create(Banner $Banner): bool;
	public function update(Banner $Banner): bool;
	public function delete(Banner $Banner): bool;

}