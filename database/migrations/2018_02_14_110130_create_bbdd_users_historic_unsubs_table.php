<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBbddUsersHistoricUnsubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::connection('temp')->create('bbdd_users_historic_unsubs', function (Blueprint $table) {
            $table->integer('id')->unasigned()->index();
            $table->integer('id_val')->unasigned()->index();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
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
        Schema::connection('temp')->dropIfExists('bbdd_users_historic_unsubs');
    }
}
