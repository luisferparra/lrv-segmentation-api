<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateStatisticsDisplaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics_displays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('displayed_at');
           
        });
        DB::table('statistics_displays')->insert([
            ['id'=>1,'displayed_at'=>'dashboard_top'],
            ['id'=>2,'displayed_at'=>'dashboard_side'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistics_displays');
    }
}
