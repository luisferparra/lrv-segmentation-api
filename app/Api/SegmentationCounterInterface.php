<?php

namespace App\Api;

use App\Models\SegmentationCounterRequest;



interface SegmentationCounterInterface{
    public function processRequest(SegmentationCounterRequest $request);



}