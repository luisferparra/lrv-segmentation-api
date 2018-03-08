<?php

namespace App\Models;

class CrmColumn extends \App\Models\Base\CrmColumn
{
	protected $fillable = [
		'column_name',
		'update_type',
		'column_has_data',
		'data_source',
		'column_front_name',
		'table_ref',
		'field_ref',
		'key_value_ref',
		'channel_type',
		'active'
	];
}
