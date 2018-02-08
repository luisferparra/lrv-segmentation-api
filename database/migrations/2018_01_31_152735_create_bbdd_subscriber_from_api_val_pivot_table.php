<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBbddSubscriberFromApiValPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('segmentation')->create('bbdd_subscriber_from_api_val', function (Blueprint $table) {
            $table->integer('bbdd_subscriber_id')->unsigned()->index();
            $table->foreign('bbdd_subscriber_id')->references('id')->on('bbdd_subscribers')->onDelete('cascade');
            $table->integer('from_api_val_id')->unsigned()->index();
            $table->foreign('from_api_val_id')->references('id')->on('from_api_vals')->onDelete('cascade');
            $table->primary(['bbdd_subscriber_id', 'from_api_val_id'], 'subscriber_api_val_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('segmentation')->drop('bbdd_subscriber_from_api_val');
    }
}
