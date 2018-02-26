<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('statistics_types_id')->unsigned()->index();
            $table->integer('statistics_displays_id')->unsigned();
            $table->integer('aaaa_table_controls_id')->unsigned()->index();
            $table->string('label');
            $table->text('data')->comment('Si es de tipo graph tendrá todos los datos, si no, será único');
            $table->string('colour')->nullable()->comment('color del elemento')->default('default');
            $table->string('icon')->default('ion ion-stats-bars');
            $table->integer('order')->default(10)->comment('Orden dentro de su tipo y display');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('statistics_types_id')->references('id')->on('statistics_types');
            $table->foreign('statistics_displays_id')->references('id')->on('statistics_displays');

            $table->foreign('aaaa_table_controls_id')->references('id')->on('aaaa_table_controls');
            
        });
        DB::table('statistics')->insert([
            ['id'=>1,'statistics_types_id'=>1,'statistics_displays_id'=>1,'aaaa_table_controls_id'=>1,'label'=>'Openers','data'=>'234765','icon'=>'ion ion-ios-personadd-outline','colour'=>'red']
        ]); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistics');
    }
}
