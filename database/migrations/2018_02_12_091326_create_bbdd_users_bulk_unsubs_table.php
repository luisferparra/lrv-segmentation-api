<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


/**
 * Tabla que se utilizará para isertar datos que se dan de baja de una bbdd (bulk unsub)
 */

class CreateBbddUsersBulkUnsubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('temp')->create('bbdd_users', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->integer('id_val')->unsigned()->index();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            
            // $table->foreign('id')->references('id')->on('bbdd_subscribers');
            
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
        Schema::connection('temp')->dropIfExists('bbdd_users');
    }
}
