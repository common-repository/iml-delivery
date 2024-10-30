<?php

namespace Iml\CalcDelivery\Api;

class ApiErrorFactory
{
    protected $method;

    public function __construct($method = '')
    {
        $this->method = $method;
    }

    public function getResponse($httpCode, $response)
    {
        switch($this->method)
        {
            case 'GetPrice': 
                return new ApiResponse\GetPrice($this, $httpCode, $response);

            default: 
                return new ApiResponse($this, $httpCode, $response);
        }
    }

    public function throwErrorResponce($errorMessage)
    {
        throw new ApiFailure($errorMessage);
    }

    public function throwErrorWithConsoleMessage($errorMessage, $consoleMessage)
    {
        throw new ApiFailure($errorMessage, $consoleMessage);
    }
}
