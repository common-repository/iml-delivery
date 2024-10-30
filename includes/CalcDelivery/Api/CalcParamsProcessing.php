<?php

namespace Iml\CalcDelivery\Api;



ini_set('serialize_precision', -1);
/**
 * Подготовка структур данных для запроса к API
 */
class CalcParamsProcessing
{
    private $calculationParametersFactory;
    private $authProvider;

    /**
     * CalcParamsProcessing constructor.
     * @param \Iml\Calculator\CalculationParametersFactory $calculationParametersFactory
     */
    public function __construct(CalculationParametersFactory $calculationParametersFactory, AuthProvider $authProvider)
    {
        $this->calculationParametersFactory = $calculationParametersFactory;
        $this->authProvider                 = $authProvider;
    }


    public function process($postParams)
    {

        $authData = $this->authProvider->getAuthData($postParams);

        $parameters = $this->calculationParametersFactory->createCalculationParameters($postParams, $postParams, $authData);

        return $parameters;
    }
}
