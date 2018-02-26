<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 22 Feb 2018 17:35:55 +0000.
 */

namespace App\Models\Base;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class StatisticsType
 * 
 * @property int $id
 * @property string $type
 * @property string $layout
 * 
 * @property \Illuminate\Database\Eloquent\Collection $statistics
 *
 * @package App\Models\Base
 */
class StatisticsType extends Eloquent
{
	use \Reliese\Database\Eloquent\BitBooleans;
	use \Volosyuk\SimpleEloquent\SimpleEloquent;
	public $timestamps = false;

	public function statistics()
	{
		return $this->hasMany(\App\Models\Statistic::class, 'statistics_types_id');
	}
}
