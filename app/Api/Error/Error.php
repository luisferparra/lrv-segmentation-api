<?php

namespace App\Api\Error;

class Error implements \JsonSerializable
{


    protected $code;
    protected $http_code;
    protected $message;

/**
 * Función que devuelve un json con array con los errores
 *
 * @param integer $http_code Código http de respuesta en el header
 * @param string $message Mensaje a devolver del error
 * @param integer $code Código adicional del error
 * @return void
 */
    public function __construct($http_code, $message, $code = null)
    {
        
        $this->code = $code;
        $this->http_code = $http_code;
        $this->message = $message;


    }

    public function getHttpCode(){
        return $this->http_code;
    }

    public function jsonSerialize()
    {
        return ['error' => array('code' => $this->code, 'http_code' => $this->http_code, 'message' => $this->message)];
    }

}
