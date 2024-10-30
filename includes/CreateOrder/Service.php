<?php

namespace Iml\CreateOrder;

class Service
{
  private $validator;
  private $apiProvider;

  public function __construct($validator, $apiProvider)
  {

    $this->validator = $validator;
    $this->apiProvider = $apiProvider;
  }

  public function createOrder($params)
  {
    $this->validator->validate($params);
    $barcode = $this->apiProvider->createOrder($params);
    return $barcode;
  }


}
