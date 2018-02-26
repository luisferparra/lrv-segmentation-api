<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 22 Feb 2018 17:36:02 +0000.
 */

namespace App\Models\Base;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class StatisticsDisplay
 * 
 * @property int $id
 * @property string $displayed_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $statistics
 *
 * @package App\Models\Base
 */
class StatisticsDisplay extends Eloquent
{
	use \Reliese\Database\Eloquent\BitBooleans;
	use \Volosyuk\SimpleEloquent\SimpleEloquent;
	public $timestamps = false;

	public function statistics()
	{
		return $this->hasMany(\App\Models\Statistic::class, 'statistics_displays_id');
	}
}
