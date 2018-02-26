<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 22 Feb 2018 17:36:08 +0000.
 */

namespace App\Models\Base;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Statistic
 * 
 * @property int $id
 * @property int $statistics_types_id
 * @property int $statistics_displays_id
 * @property int $aaaa_table_controls_id
 * @property string $label
 * @property string $data
 * @property string $colour
 * @property string $icon
 * @property int $order
 * @property bool $active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\AaaaTableControl $aaaa_table_control
 * @property \App\Models\StatisticsDisplay $statistics_display
 * @property \App\Models\StatisticsType $statistics_type
 *
 * @package App\Models\Base
 */
class Statistic extends Eloquent
{
	use \Reliese\Database\Eloquent\BitBooleans;
	use \Volosyuk\SimpleEloquent\SimpleEloquent;

	protected $casts = [
		'statistics_types_id' => 'int',
		'statistics_displays_id' => 'int',
		'aaaa_table_controls_id' => 'int',
		'order' => 'int',
		'active' => 'bool'
	];

	public function aaaa_table_control()
	{
		return $this->belongsTo(\App\Models\AaaaTableControl::class, 'aaaa_table_controls_id');
	}

	public function statistics_display()
	{
		return $this->belongsTo(\App\Models\StatisticsDisplay::class, 'statistics_displays_id');
	}

	public function statistics_type()
	{
		return $this->belongsTo(\App\Models\StatisticsType::class, 'statistics_types_id');
	}
}
