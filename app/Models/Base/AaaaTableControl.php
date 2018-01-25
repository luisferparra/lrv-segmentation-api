<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 23 Jan 2018 15:33:40 +0000.
 */

namespace App\Models\Base;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AaaaTableControl
 * 
 * @property int $id
 * @property string $name
 * @property string $action
 * @property string $description
 * @property string $api_name
 * @property int $data_type_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\DataType $data_type
 *
 * @package App\Models\Base
 */
class AaaaTableControl extends Eloquent
{
	use \Reliese\Database\Eloquent\BitBooleans;
	use \Volosyuk\SimpleEloquent\SimpleEloquent;

	protected $casts = [
		'data_type_id' => 'int'
	];

	public function data_type()
	{
		return $this->belongsTo(\App\Models\DataType::class);
	}
}
