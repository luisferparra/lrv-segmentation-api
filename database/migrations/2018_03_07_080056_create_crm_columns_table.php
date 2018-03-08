<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('crm-data')->create('crm_columns', function (Blueprint $table) {
            $table->integer('id')->unsigned()->primary('id');
            $table->string('column_name');
            $table->string('update_type')->index()->comment('Values UPDATE: only 1 value allowed, SEARCH: multiple values');
            $table->unsignedTinyInteger('column_has_data')->comment('Type of data. 1:select multiple, 2:date field 3:bit 4:numeric fields, 5:textarea');
            $table->unsignedTinyInteger('data_source')->comment('data source 1:pixel or info 2:usesrs table');

            $table->string('column_front_name')->comment('Name displayed at the CRM');
            $table->string('table_ref')->nullable()->comment('If not empty, crm table where the values are taken');
            $table->string('field_ref')->nullable()->comment('if table_ref, fields of key=>value');
            $table->string('key_value_ref')->nullable()->comment('if table_ref, fields of key=>value');


            $table->string('channel_type')->default('1')->comment('Tipo de dato, 1:EML 2:TELF');
            $table->boolean('active')->default(true)->comment('Si está activa la columna en el CRM');
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
        Schema::connection('crm-data')->dropIfExists('crm_columns');
    }
}