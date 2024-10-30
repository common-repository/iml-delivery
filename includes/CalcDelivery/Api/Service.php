<?php

namespace Iml\CalcDelivery\Api;


class Service
{
  private $apiProvider;
  private $paramsProcessing;

  public function __construct(
    ApiProvider $apiProvider,
    CalcParamsProcessing $paramsProcessing
  ) {
    $this->apiProvider = $apiProvider;
    $this->paramsProcessing = $paramsProcessing;
  }

  public function calculateDeliveryConditions($parameters)
  {
    $calculationParameters = $this->paramsProcessing->process($parameters);
    return $this->apiProvider->calculateDeliveryParams($calculationParameters);
  }


}
