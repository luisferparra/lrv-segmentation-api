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
            $table->integer('id')->unasigned();
            $table->integer('id_val')->unasigned()->index();
            $table->timestamps();
            
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
