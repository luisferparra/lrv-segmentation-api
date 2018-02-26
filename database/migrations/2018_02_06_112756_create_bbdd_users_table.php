<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBbddUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('segmentation')->create('bbdd_users', function (Blueprint $table) {
            $table->integer('id')->unasigned();
            $table->integer('id_val')->unasigned();
           // $table->foreign('id')->references('id')->on('bbdd_subscribers');
            $table->foreign('id')->references('id')->on('bbdd_lists');
            $table->primary(['id', 'id_val']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('segmentation')->dropIfExists('bbdd_users');
    }
}
