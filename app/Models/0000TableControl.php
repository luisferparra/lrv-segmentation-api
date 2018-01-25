<?php

namespace App\Models;

class 0000TableControl extends \App\Models\Base\0000TableControl
{
	protected $fillable = [
		'name',
		'action',
		'description',
		'api_name',
		'data_type_id'
	];

	protected $hidden = ['name','action','created_at','modified_at'];
}
