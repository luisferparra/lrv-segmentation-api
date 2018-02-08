<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_loads', function (Blueprint $table) {
            

            $table->increments('id');
            $table->string('functionality');
            $table->string('api_name')->nullable()->index();
            $table->longText('request');
            $table->longText('response')->nullable();
            $table->longText('response_errors')->nullable();
            $table->integer('cont_input')->unassigned()->default(0);
            $table->integer('cont_processed')->unassigned()->default(0);
            $table->boolean('processed')->default(0)->index();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_loads');
    }
}
