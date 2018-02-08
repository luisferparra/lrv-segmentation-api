<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
/**
 * Clase que devuelve un item de info de la tabla de control
 */
class SegmentationInfoResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //dd($this);
        return [
            'description' => $this->description,
            'api_name'=>$this->api_name,
            'data_type'=>$this->data_type->name
        ];
        //return parent::toArray($request);
    }
}
