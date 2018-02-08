<?php

namespace App\Api\Error;

use Illuminate\Http\JsonResponse;


class ErrorResponse extends JsonResponse
{

    public function __construct(Error $data, $headers = [], $options = 0)
    {
        parent::__construct($data,$data->getHttpCode(),$headers, $options);
    }
}