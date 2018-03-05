<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBbddListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('segmentation')->create('bbdd_lists', function (Blueprint $table) {
            $table->integer('id')->unsigned()->primary();
            $table->string('val',5)->unique();
            $table->boolean('active')->default(1)->index();
            $table->timestamps();
        });
        DB::connection('segmentation')->table('bbdd_lists')->insert(
            [
                ['id'=>1, 'val'=>'NET', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>2, 'val'=>'FST', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>3, 'val'=>'FXD', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>4, 'val'=>'MXD', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>5, 'val'=>'FTL', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>6, 'val'=>'LMX', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>7, 'val'=>'SAP', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>8, 'val'=>'KPD', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>9, 'val'=>'DJK', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>10, 'val'=>'PCM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>11, 'val'=>'TLN', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>12, 'val'=>'SEM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>13, 'val'=>'BYP', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>14, 'val'=>'MRS', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>15, 'val'=>'MRC', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>16, 'val'=>'VIA', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>17, 'val'=>'SAN', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>18, 'val'=>'GAT', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>19, 'val'=>'CPY', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>20, 'val'=>'MM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>21, 'val'=>'BRA', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>22, 'val'=>'DUP', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>23, 'val'=>'JAZ', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>24, 'val'=>'APR', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>25, 'val'=>'CAS', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>26, 'val'=>'BOR', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>27, 'val'=>'CON', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>28, 'val'=>'AVD', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>29, 'val'=>'LEG', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>30, 'val'=>'NAT', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>31, 'val'=>'NEC', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>32, 'val'=>'ALP', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>33, 'val'=>'DOS', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>34, 'val'=>'BRU', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>35, 'val'=>'EAS', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>36, 'val'=>'VIP', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>37, 'val'=>'KAN', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>38, 'val'=>'REG', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>39, 'val'=>'GOU', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>40, 'val'=>'SDL', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>41, 'val'=>'WPY', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>42, 'val'=>'MGM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>43, 'val'=>'WEN', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>44, 'val'=>'ATR', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>45, 'val'=>'GTP', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>46, 'val'=>'TTR', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>47, 'val'=>'NTR', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>48, 'val'=>'CVP', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>49, 'val'=>'AVL', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>50, 'val'=>'NSV', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>51, 'val'=>'SBS', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>52, 'val'=>'INA', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>53, 'val'=>'PRO', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>54, 'val'=>'SHO', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>55, 'val'=>'L4S', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>56, 'val'=>'NT3', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>57, 'val'=>'MTC', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>58, 'val'=>'CTR', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>59, 'val'=>'GTH', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>60, 'val'=>'DUM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>61, 'val'=>'CSM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>62, 'val'=>'BLD', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>63, 'val'=>'SSM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>64, 'val'=>'CMR', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>65, 'val'=>'RC1', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>66, 'val'=>'RP2', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>67, 'val'=>'MGV', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>68, 'val'=>'SCE', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>69, 'val'=>'EAF', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>70, 'val'=>'ACT', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>71, 'val'=>'PAR', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>72, 'val'=>'MDC', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>73, 'val'=>'SCF', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>74, 'val'=>'OPT', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>75, 'val'=>'APY', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>76, 'val'=>'SRP', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>77, 'val'=>'LEF', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>78, 'val'=>'SES', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>79, 'val'=>'SGD', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>80, 'val'=>'VNC', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>81, 'val'=>'ASM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>82, 'val'=>'CPT', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>83, 'val'=>'LDM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>84, 'val'=>'MSM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>85, 'val'=>'UJK', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>86, 'val'=>'LCT', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>88, 'val'=>'PPI', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>89, 'val'=>'PPF', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>90, 'val'=>'TDN', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>91, 'val'=>'IBE', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>92, 'val'=>'CTI', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>93, 'val'=>'YED', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>94, 'val'=>'EDF', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>95, 'val'=>'GTM', 'active'=>0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>96, 'val'=>'ITR', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>97, 'val'=>'VTP', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
['id'=>98, 'val'=>'DGS', 'active'=>1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')]
            ]
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('segmentation')->dropIfExists('bbdd_lists');
    }
}
