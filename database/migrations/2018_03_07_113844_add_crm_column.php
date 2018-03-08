<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCrmColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aaaa_table_controls', function (Blueprint $table) {
            $table->integer('crm_columns_id')->unsigned()->nullable()->after('data_type_id');
            $table->foreign('crm_columns_id')->references('id')->on('crm-data.crm_columns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aaaa_table_controls', function (Blueprint $table) {
            $table->dropColumn(['crm_columns_id']);
        });
    }
}
