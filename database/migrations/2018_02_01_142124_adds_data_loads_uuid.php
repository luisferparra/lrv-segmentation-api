<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddsDataLoadsUuid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_loads', function (Blueprint $table) {
            //
            $table->uuid('uuid_token')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_loads', function (Blueprint $table) {
            //
            $table->dropColumn(['uuid_token']);
        });
    }
}
