<?php
namespace Iml\ImlOrder;

abstract class Proxy
{
  protected $order;
  protected $data = [];

  abstract public function getData();

  public function __construct($order)
  {
    $this->order  = $order;
  }

  protected function convert2Bool($value)
  {
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  protected function convert2Int($value, $ifFalse = 0)
  {

    $result = filter_var($value, FILTER_VALIDATE_INT);
    if ($result === false) {
      return $ifFalse;
    }

    return $result;
  }

  protected function convert2Float($value, $ifFalse = 0)
  {

    $value = str_replace(',', '.', $value);
    $result = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($result === false) {
      return $ifFalse;
    }

    return round((float)$result, 3);
  }


  protected function convertMainProperties()
  {
    $this->order->Amount = (!empty($this->order->Amount)) ? $this->convert2Int($this->order->Amount) : 0;
    $this->order->ValuatedAmount = (!empty($this->order->ValuatedAmount)) ? $this->convert2Int($this->order->ValuatedAmount) : 0;
    $this->order->Volume = (!empty($this->order->Volume)) ? $this->convert2Int($this->order->Volume) : 0;
    $this->order->Weight = (!empty($this->order->Weight)) ? $this->convert2Float($this->order->Weight) : 0;
  }


}
