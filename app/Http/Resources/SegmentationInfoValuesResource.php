<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SegmentationInfoValuesResource extends Resource
{


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!empty($this->action) && $this->action=='bit') {
            return [
                'id' => 1,
                'crm_value' => 1,
                'front_value' => 1
            ];
        }
            
        return [
            'id' => $this->id,
            'crm_value'=>$this->val_crm,
            'front_value'=>$this->val_normalized
        ];
    }
}
