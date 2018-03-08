<?php

namespace App\Console\Commands\crm;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

class columnsSynchro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:sync:columns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that will synchronize Columns data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $crmColumns = DB::connection('crm')->table('pixel_column')->get();
        $arrIns = [];
        foreach ($crmColumns as $column) {
            $arrIns[] = [
                'id'=>$column->id_column,
                'column_name'=>$column->columna,
                'update_type'=>$column->update_type,
                'column_has_data'=>$column->column_has_data,
                'data_source'=>$column->data_source,
                'column_front_name'=>ucwords(trim($column->column_front_name)),
                'table_ref'=>$column->table_ref,
                'field_ref'=>$column->field_ref,
                'key_value_ref'=>$column->key_value_ref,
                'channel_type'=>$column->channel_type,
                'active'=>$column->b_activa,
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')


            ];
        }
        DB::connection('crm-data')->table('crm_columns')->insertOnDuplicateKey($arrIns,['updated_at'=>Carbon::now()->format('Y-m-d H:i:s'),'column_name',
        'update_type',
        'column_has_data',
        'data_source',
        'column_front_name',
        'table_ref',
        'field_ref',
        'key_value_ref',
        'channel_type',
        'active']);
        
    }
}
