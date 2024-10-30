<?php

namespace Iml\ImlOrder;

class CalcDeliveryProxy extends Proxy
{

  public function __construct($order, $method = 'GetPlantCostOrder')
  {
    $this->order  = $order;
    $this->method = $method;
  }

  public function getData()
  {
    $method = 'getData' . $this->method;

    if (method_exists($this, $method)) {
      return call_user_func_array([$this, $method], func_get_args());
    }

    return $this->getDataGetPlantCostOrder();
  }

  public function getDataGetPlantCostOrder ()
  {
    $this->convertMainProperties();
    //наложенный платеж
    $this->data['Amount'] = $this->order->Amount;
    // оценочная стоимость
    $this->data['ValuatedAmount'] = $this->order->ValuatedAmount;
    $this->data['Volume'] = $this->order->Volume;
    $this->data['Weight'] = 0;
    $this->data['Job'] = $this->order->Job;

    $isCashService = $this->order->isCashService;



    $this->data['RegionCodeFrom'] = $this->order->RegionCodeFrom;
    $this->data['RegionCodeTo']   = $this->order->RegionCodeTo;
    $this->data['DetailedResult'] = $this->order->DetailedResult;

    $orderItemRequestArray = array();

    if (!empty($this->order->volumeAr)) {
      foreach ($this->order->volumeAr as $volumeItem) {
        $item = array(
          'productNo' => 10000,
          'itemType'   => 7, //грузовое место
          'weightLine' => $this->convert2Float($volumeItem['Weight']),
          'Side1'      => $volumeItem['Length'],
          'Side2'      => $volumeItem['Width'],
          'Side3'      => $volumeItem['Height'],
        );
        $this->data['Weight'] += $this->convert2Float($volumeItem['Weight']);
        $orderItemRequestArray[] = (object) $item;
      }
    }

    if (!empty($this->order->conditionItems)) {
      foreach ($this->order->conditionItems as $conditionItem) {
        $item = array(
          'productNo' => $conditionItem['productNo'],
          'itemType'   => $conditionItem['itemType'],
          'allowed'   => $conditionItem['allowed']
        );
        $orderItemRequestArray[] = (object) $item;
      }
    }
    if(isset($this->data['DeliveryPoint']))
    {
      unset($this->data['DeliveryPoint']);
    }

    // невозможно КО
    if (!$this->order->hasCashService()) {
      $this->data['Amount'] = 0;
    }

    if($this->order->hasCourierService())
    {
      if (!empty(trim($this->order->Address))) {
        // указан полный адрес курьерской доставки
        $this->data['Address'] = $this->order->Address;
      } else {
        if (isset($this->order->toPlace) &&
        // для москвы - не дополнять адресную строку
        (mb_stripos($this->order->toPlace, 'МОСКВА', 0, 'UTF-8') === false)) {
          // укажем в адресе конкретный город назначения
          $this->data['Address'] = trim($this->order->toPlace);
        }
      }
    }else {
      $this->data['DeliveryPoint'] = $this->order->DeliveryPoint;
    }

    $this->data['GoodItems'] = $orderItemRequestArray;


    return $this->data;

  }

  public function getDataGetPrice()
  {
    $data = $this->getDataGetPlantCostOrder();

    return [
      'job'             => $data['Job'],
      'weigth'          => $data['Weight'],
      'volume'          => $data['Volume'],
      // 'specialCode'     => $params['DeliveryPointCode'],
      // 'receiveDate'     => $params['ShipmentDate'],
      'receiptAmount'   => $data['Amount'],
      'declaredValue'   => $data['ValuatedAmount'],
      'regionFrom'      => $data['RegionCodeFrom'],
      'regionTo'        => $data['RegionCodeTo'],
      // 'indexTo'         => $params['IndexTo'],
      // 'indexFrom'       => $params['IndexFrom'],
      'deliveryAddress' => $data['Address'],
    ];
  }


}
