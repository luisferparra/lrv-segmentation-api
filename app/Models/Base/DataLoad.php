<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 30 Jan 2018 15:15:32 +0000.
 */

namespace App\Models\Base;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DataLoad
 * 
 * @property int $id
 * @property string $functionality
 * @property string $request
 * @property string $response
 * @property string $response_errors
 * @property int $cont_input
 * @property int $cont_processed
 * @property bool $processed
 * @property \Carbon\Carbon $processed_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models\Base
 */
class DataLoad extends Eloquent
{
	use \Reliese\Database\Eloquent\BitBooleans;
	use \Volosyuk\SimpleEloquent\SimpleEloquent;

	protected $casts = [
		'cont_input' => 'int',
		'cont_processed' => 'int',
		'processed' => 'bool'
	];

	protected $dates = [
		'processed_at'
	];
}
