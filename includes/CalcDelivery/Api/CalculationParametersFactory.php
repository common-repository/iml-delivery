<?php

namespace Iml\CalcDelivery\Api;

class CalculationParametersFactory
{

    public function createCalculationParameters($data, $postParams, $authData)
    {
        return new CalculationParameters($data, $postParams, $authData);
    }

}
