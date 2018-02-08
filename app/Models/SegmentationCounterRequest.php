<?php

namespace App\Models;

class SegmentationCounterRequest extends \App\Models\Base\SegmentationCounterRequest
{
	protected $hidden = [
		'uuid_token'
	];

	protected $fillable = [
		'request',
		'user_id',
		'uuid_token',
		'response'
	];
}
