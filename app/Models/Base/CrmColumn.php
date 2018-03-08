<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 07 Mar 2018 12:10:44 +0000.
 */

namespace App\Models\Base;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CrmColumn
 * 
 * @property int $id
 * @property string $column_name
 * @property string $update_type
 * @property int $column_has_data
 * @property int $data_source
 * @property string $column_front_name
 * @property string $table_ref
 * @property string $field_ref
 * @property string $key_value_ref
 * @property string $channel_type
 * @property bool $active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $aaaa_table_controls
 *
 * @package App\Models\Base
 */
class CrmColumn extends Eloquent
{
	use \Reliese\Database\Eloquent\BitBooleans;
	use \Volosyuk\SimpleEloquent\SimpleEloquent;
	protected $connection = 'crm-data';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'column_has_data' => 'int',
		'data_source' => 'int',
		'active' => 'bool'
	];

	public function aaaa_table_controls()
	{
		return $this->hasMany(\App\Models\AaaaTableControl::class, 'crm_columns_id');
	}
}
