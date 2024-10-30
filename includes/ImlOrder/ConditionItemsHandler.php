<?php

namespace Iml\ImlOrder;

class ConditionItemsHandler
{

  private $order;

  public function __construct($order)
  {
    $this->order = $order;
  }

  public function resetAllConditions()
  {
      $this->order->condMap = [];
      $this->order->conditionItems = [];
  }


  public function setCondition($optionName, $code, $allowed)
  {
    $this->order->condMap[$optionName] = $allowed;
    $this->addCondition($code, 10, $allowed);
  }

  private function addCondition($productNo, $itemType, $allowed = true)
  {
    $allowed = (int)$allowed;
    $this->order->conditionItems[] = compact('productNo', 'itemType', 'allowed');
  }

  public function getConditionList()
  {
    return $this->order->condMap;
  }

}
