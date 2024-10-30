<?php

namespace Iml\ImlOrder;

class CreateOrderProxy extends Proxy
{


  public function getCustomerOrderNumber()
  {
    return  rand(10000, 90000);
  }


  public function getData()
  {
    $this->convertMainProperties();

    $this->data['DeliveryDate'] = $this->order->DeliveryDate;
    $this->data['Amount'] = $this->order->Amount;
    // оценочная стоимость
    $this->data['ValuatedAmount'] = $this->order->ValuatedAmount;
    $this->data['Weight'] = 0;
    $this->data['Job'] = $this->order->Job;



    $this->data['RegionCodeFrom'] = $this->order->RegionCodeFrom;
    $this->data['RegionCodeTo']   = $this->order->RegionCodeTo;
// $this->order->CustomerOrder
    $this->data['CustomerOrder'] = $this->order->CustomerOrder;
    $this->data['Contact'] = $this->order->Contact;
    $this->data['Address'] = $this->order->Address;
    $this->data['Phone'] = $this->order->Phone;
    $this->data['Comment'] = $this->order->Comment;
    $this->data['Email'] = $this->order->Email;
    if($this->order->Test)
    {
      $this->data['Test'] = $this->order->Test;
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




  $hasCashService = $this->order->hasCashService();

  $this->data['Amount'] = ($hasCashService) ? $this->data['Amount'] : 0;

  $enableValuatedAmount = !empty($this->data['ValuatedAmount']);
  // разрешить расчет оценочной стоимости
  if($enableValuatedAmount)
  {
    $this->data['ValuatedAmount'] = 0;
  }

  $this->data['GoodItems'] = [];
  foreach ($this->order->goodItems as $goodItem)
  {

    // если оценочная стоимость больше 0, то подсчитать ее в сумме от товаров
     if($enableValuatedAmount)
     {
       $this->data['ValuatedAmount'] += isset($goodItem['statisticalValueLine']) ? $goodItem['statisticalValueLine'] * $goodItem["itemQuantity"] : 0;
     }
    // if($hasCashService)
    // {
      // рассчитаем Amount
      // $this->data['Amount'] += isset($goodItem['amountLine']) ? $goodItem['amountLine'] * $goodItem["itemQuantity"] : 0;
    // }
    $this->data['GoodItems'][] = array_merge($goodItem,
    [
    'amountLine' => ($hasCashService) ? $goodItem['amountLine'] : 0,
    'statisticalValueLine' => ($enableValuatedAmount) ? $goodItem['statisticalValueLine'] : 0,
    'VATRate' => $this->order->VAT,
    'productBarCode' => rand(10000000, 99999999)
  ]);
  }



  $placesAr = [];
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
      $placesAr[] = $item;
    }
  }


    $this->data['Volume'] = count($this->order->volumeAr);
    // добавляем условия
    $this->data['GoodItems'] = array_merge($this->data['GoodItems'], $this->order->conditionItems, $placesAr);



    if($this->order->enableFullfilment)
    {
      $this->data['GoodItems'][] = ["productNo" => "10000", "itemType" => "14", "allowed" => "1", "productBarCode" => "00000"];
    }

    // allowed - здесь, возможность отказаться от посылки без оплаты
    // добавляем услугу - доставка


    $deliveryCost = ($hasCashService) ? $this->order->DeliveryCost : 0;

    $this->data['GoodItems'][] = ["productNo" => "Доставка", "itemType" => 3, "allowed" => 0, 'amountLine' => $deliveryCost];


    // указываем подробность, что  заказ создан в Wordpress
    $this->data['GoodItems'][] = ["ProductNo" => 10000, 'itemType' => 78,'ProductName' => 'WordPress', "productBarCode" => 00000];


    //наложенный платеж
    $this->data['Amount'] += $deliveryCost;

    // ___p($this->data);
    // die();

    return $this->data;

  }



}
