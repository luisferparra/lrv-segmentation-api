<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateTimeMachineEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('segmentation')->create('time_machine_events', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->index();
            $table->unsignedInteger('year')->index();
            $table->unsignedTinyInteger('trimester');
            $table->unsignedTinyInteger('quarter');
            $table->unsignedTinyInteger('month')->index();
            $table->unsignedTinyInteger('day')->index();
            $table->unsignedTinyInteger('week');
            $table->unsignedTinyInteger('day_of_week')->comment('Day of the week. Monday: 1');
            $table->unsignedInteger('day_of_year')->comment('day of the Year. 1970-01-01 is day 1');
        });
        /**
         * Generamos los datod
         */
        $arrIns = [];
        $dateBegin = Carbon::create(1900, 1, 1);
        $currDate = $dateBegin;
        $dateEnd = Carbon::create(2200, 12, 31);

        $counter = 0;

        while ($currDate <= $dateEnd) {
           // dump($currDate->toDateString());
            $month = $currDate->month;
            $trimester = ($month<=4) ? 1 : (($month>=5 && $month<=8) ? 2 : 3);
            $arrIns[] = [
                'date' => $currDate->toDateString(),
                'year' => $currDate->year,
                'trimester' => $trimester,
                'quarter' => $currDate->quarter,
                'month' => $month,
                'day' => $currDate->day,
                'week' => $currDate->weekOfYear,
                'day_of_week' => $currDate->dayOfWeek + 1,
                'day_of_year' => $currDate->dayOfYear + 1
            ];
            $currDate->addDay();
            $counter++;
            if ($counter == 3000) {
                $counter = 0;
                DB::connection('segmentation')->table('time_machine_events')->insert($arrIns);
                unset($arrIns);
                $arrIns = [];
            }
        }
        if (!empty($arrIns)) {
            DB::connection('segmentation')->table('time_machine_events')->insert($arrIns);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('segmentation')->dropIfExists('time_machine_events');
    }
}
