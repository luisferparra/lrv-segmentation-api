<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\DataLoad;
use App\Api\LoadDataProcess;

/*

Test Cases Para createUsersByApiNameCRMData

{
	"input_data_mode":"id",
  "data": [
			{
			"id":1,
			"val":[1,3,5,9]
		}	,
		 {
			"id":2,
			"val":[13]
		}
	]
  
}


{
	"input_data_mode":"crm",
  "data": [
			{
			"id":"HOTMAIL",
			"val":[1,3,5,9]
		}	,
		 {
			"id":"LIVE",
			"val":[13]
		}
	]
  
}


{
	"input_data_mode":"val",
  "data": [
			{
			"id":"GMAIL.COM",
			"val":[1,3,5,9]
		}	,
		 {
			"id":"MSN.ES",
			"val":[13]
		}
	]
  
}

*/

class ProcessDataLoadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

protected $dataLoad;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DataLoad $dataLoad)
    {
        //
        $this->dataLoad = $dataLoad;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $dataLoadObject = new LoadDataProcess($this->dataLoad);
        $errors = $dataLoadObject->getErrors();
        $itemsProcessed = $dataLoadObject->getProcessedItems();       // dd($this->dataLoad);
        $this->dataLoad->processed = 1;
        $this->dataLoad->processed_at = date('Y-m-d H:i:s');
        $this->dataLoad->cont_processed = $itemsProcessed;
        $this->dataLoad->cont_input = $dataLoadObject->getProcessedUsers();
        $this->dataLoad->response_errors = json_encode($errors);
        $this->dataLoad->response = 'OK';
        $this->dataLoad->save();
    }
}
