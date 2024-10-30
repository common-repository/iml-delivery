<?php


namespace Iml\Statuses;

class Service
{

  private $apiProvider;

  public function __construct($apiProvider)
  {
    $this->apiProvider = $apiProvider;
  }

  public function getStatuses($params)
  {
    $result = $this->apiProvider->getStatuses($params);
    return $result;
  }

}
