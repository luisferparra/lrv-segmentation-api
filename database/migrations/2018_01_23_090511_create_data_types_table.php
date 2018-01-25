<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTypesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('data_types', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
		});
		DB::table('data_types')->insert([
			['id' => 1, 'name' => 'simple'],
			['id' => 2, 'name' => 'multiple'],
		]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('data_types');
	}
}
