<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentationCounterRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segmentation_counter_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kid')->unasigned()->index()->default(0);
            $table->longText('request');
            $table->integer('user_id')->nullable()->unsigned()->index();
            $table->uuid('uuid_token')->unique();
            $table->longText('response')->nullable();
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
        Schema::dropIfExists('segmentation_counter_requests');
    }
}
