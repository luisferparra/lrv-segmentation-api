<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 05 Feb 2018 09:25:38 +0000.
 */

namespace App\Models\Base;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SegmentationCounterRequest
 * 
 * @property int $id
 * @property string $request
 * @property int $user_id
 * @property string $uuid_token
 * @property string $response
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models\Base
 */
class SegmentationCounterRequest extends Eloquent
{
	use \Reliese\Database\Eloquent\BitBooleans;
	use \Volosyuk\SimpleEloquent\SimpleEloquent;

	protected $casts = [
		'user_id' => 'int'
	];
}
