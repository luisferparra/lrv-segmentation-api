<?php

namespace App\Models;

class DataLoad extends \App\Models\Base\DataLoad
{
	protected $fillable = [
		'functionality',
		'request',
		'response',
		'response_errors',
		'cont_input',
		'cont_processed',
		'processed',
		'processed_at'
	];

	protected $hidden = ['id'];
}
