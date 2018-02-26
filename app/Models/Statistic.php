<?php

namespace App\Models;

class Statistic extends \App\Models\Base\Statistic
{
	protected $fillable = [
		'id',
		'statistics_types_id',
		'statistics_displays_id',
		'aaaa_table_controls_id',
		'label',
		'data',
		'colour',
		'icon',
		'order',
		'active'
	];
}
