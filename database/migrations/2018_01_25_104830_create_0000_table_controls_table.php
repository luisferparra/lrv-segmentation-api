<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Api\SegmentationSchema;

class Create0000TableControlsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('aaaa_table_controls', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', 124);
			$table->enum('action', ['bit', 'ignore', 'normal'])->default('bit')->index();
			$table->string('description');
			$table->string('api_name')->nullable();
			$table->integer('data_type_id')->unsigned();

			$table->foreign('data_type_id')->references('id')->on('data_types');
			$table->timestamps();
		});

		DB::table('aaaa_table_controls')->insert([
			['id' => 1, 'name' => 'marketing_openers', 'action' => 'bit', 'description' => 'Usuario abridor (1)', 'api_name' => 'marketing-opener', 'data_type_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
			['id' => 2, 'name' => 'marketing_purchasers', 'action' => 'bit', 'description' => 'Usuario Comprador (1)', 'api_name' => 'marketing-purchaser', 'data_type_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
			['id' => 3, 'name' => 'marketing_clickers', 'action' => 'bit', 'description' => 'Usuario Clickador (1)', 'api_name' => 'marketing-clicker', 'data_type_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],

			['id' => 4, 'name' => 'P161', 'action' => 'ignore', 'description' => 'Platform161 Deprecated', 'api_name' => null, 'data_type_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
			['id' => 5, 'name' => 'id_channel', 'action' => 'ignore', 'description' => 'Identificador Usuario', 'api_name' => null, 'data_type_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
			['id' => 6, 'name' => 'bbdd_users', 'action' => 'ignore', 'description' => 'BBDD Alta Usuario', 'api_name' => null, 'data_type_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
			['id' => 7, 'name' => 'segmentation_util', 'action' => 'ignore', 'description' => 'Si el dato es útil', 'api_name' => null, 'data_type_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],

		]);

		//Creamos las tablas
		$segmentSchema = new SegmentationSchema('_val');
		$segmentSchema->setAllowCreateAndRemove(true);
		$segmentSchema->postCreateTableSystem('marketing_openers',true);
		$segmentSchema->postCreateTableSystem('marketing_clickers',true);
		$segmentSchema->postCreateTableSystem('marketing_purchasers',true);

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('aaaa_table_controls');
		Schema::connection('segmentation')->dropIfExists('marketing_openers');
		Schema::connection('segmentation')->dropIfExists('marketing_clickers');
		Schema::connection('segmentation')->dropIfExists('marketing_purchasers');
		Schema::connection('temp')->dropIfExists('marketing_openers');
		Schema::connection('temp')->dropIfExists('marketing_clickers');
		Schema::connection('temp')->dropIfExists('marketing_purchasers');
	}
}
