<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 23 Jan 2018 15:33:46 +0000.
 */

namespace App\Models\Base;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DataType
 * 
 * @property int $id
 * @property string $name
 * 
 * @property \Illuminate\Database\Eloquent\Collection $aaaa_table_controls
 *
 * @package App\Models\Base
 */
class DataType extends Eloquent
{
	use \Reliese\Database\Eloquent\BitBooleans;
	use \Volosyuk\SimpleEloquent\SimpleEloquent;
	public $timestamps = false;

	public function aaaa_table_controls()
	{
		return $this->hasMany(\App\Models\AaaaTableControl::class);
	}
}
