<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateStatisticsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->text('layout')->nullable();
        });
        DB::table('statistics_types')->insert([
			['id' => 1, 'type' => 'number','layout'=>'<div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-[[COLOR]]"><i class="[[ICON]]]"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">[[LABEL]]]</span>
                <span class="info-box-number">[[NUMBER]]<small></small></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>'],
            ['id' => 2, 'type' => 'graph-line','layout'=>null],
            ['id' => 3, 'type' => 'graph-donut','layout'=>null],
            ['id' => 4, 'type' => 'small-box','layout'=>null],
		]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistics_types');
    }
}
