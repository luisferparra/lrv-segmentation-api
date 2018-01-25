<?php

namespace App\Models;

class AaaaTableControl extends \App\Models\Base\AaaaTableControl {
	protected $fillable = [
		'name',
		'action',
		'description',
		'api_name',
		'data_type_id',
	];

	protected $hidden = ['id', 'name', 'action', 'data_type_id', 'created_at', 'updated_at'];
}
