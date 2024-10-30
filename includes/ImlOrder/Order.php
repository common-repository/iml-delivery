<?php

namespace Iml\ImlOrder;

class Order
{

  public $Job; // (string): услуга доставки, Code из справочника услуг ,
  // public $ShipmentDate; // (string, optional): дата доставки заказа на склад IML в строковом представлении, формат yyyy.MM.dd ,
  public $Volume; // (integer, optional): количество мест ,
  public $Weight; // (number, optional): вес Weigth ,
  public $DeliveryPoint; // (string, optional): пункта самовывоза, RequestCode из таблицы пунктов самовывоза ,
  public $DeliveryPointCode; // (string, optional): пункта самовывоза, RequestCode из таблицы пунктов самовывоза ,
  public $RegionCodeTo; // (string, optional): код региона получения, Code из таблицы регионов ,
  public $RegionCodeFrom; // (string, optional): код региона отправления, Code из таблицы регионов ,
  public $CustomerLocation; // (string, optional): Для Склад - Код пункта самовывоза, с которого осуществятся отправка заказа физического лица (С2С). Только для самовывозных услуг, параметр RequestCode в соответствующем справочнике, находится по адресу http://list.iml.ru/sd. ,
  public $IndexFrom; // (string, optional): индекс региона отправления, альтернатива RegionCodeFrom ,
  public $IndexTo; // (string, optional): индекс региона получения, альтернатива RegionCodeTo ,
  public $Address; // (string, optional): адрес доставки ,
  public $TimeTo; // (string, optional): конец временного периода доставки ,
  public $TimeFrom; // (string, optional): начало временного периода доставки ,
  public $ValuatedAmount; // (number): оценочная стоимость заказа ,
  public $Amount; // (number): сумма заказа ,
  public $Comment; // (string, optional): комментарий ,
  public $City; // (string, optional): город доставки, для отправки почтой России ? ,
  public $PostCode; // (string, optional): индекс, для отправки почтой России ? ,
  public $PostRegion; // (string, optional): регион, для отправки почтой России ? ,
  public $PostArea; // (string, optional): район, для отправки почтой России ? ,
  public $PostContentType; // (integer, optional): тип вложения (0 - Печатная, 1 - Разное, 2 - 1 Класс), для отправки почтой России ? ,
  // public $GoodItems; // (Array[OrderItemRequest], optional): позиции заказа, если указывались при создании заказа, тип значения – массив элементов ,
  public $OrderStatuses; // (Array[OrderStatus], optional): Статусы заказа для которых нужно производить расчет На вход принимаются статусы Deliveried или 0 Доставлен (по умолчанию), Error Delivery или 1 Клиент отказался от заказа при встрече Canceled или 2 Клиент отказался от заказа заранее Transferred или 3 Доставка заказа перенесена на другой день Part.Delivery или 4 Заказ частично доставлен клиенту Exchange или 5 Заказ забран у клиента (по умолчанию для возврата) можно указывать несколько статусов ,
  public $DetailedResult; // (boolean, optional): Флаг (true, false) в случае указания true, ответ будет содержать детализация по статьям расходов.

  public $serviceName;
  public $addressPVZ;
  public $isCourierDelivery;
  public $DeliveryDate;
  public $delivery;

  public $keyFrom;
  public $keyTo;
  public $imlBarcode;
  public $isCashService;


  public $goodItems = [];
  public $conditionItems = [];


  public $VAT;
  public $wasPayed;
  public $CustomerOrder;



//4 create order
  public $Test;

  public $Email;
  public $Contact;
  public $Phone;


  public $imlStatus;

  // рассчитанная стоимость доставки
  public $DeliveryCost;

  public $volumeAr = [];

  public $condMap = [];

  public $enableFullfilment = false;



  public function hasCourierService()
  {
    return in_array($this->Job, ['24', '24КО']);
  }

  public function hasCashService()
  {
    return in_array($this->Job, ['24КО', 'С24КО']);
  }


  public function deleteGoods()
  {
    $this->goodItems = [];
  }

  public function addGoods($productNo, $productName, $weightLine, $amountLine,
  $statisticalValueLine, $itemQuantity,  $Length, $Height, $Width)
  {
    // добавляем каждый экземпляр товара для возможности частичного возврата
    // 'productBarCode' =>
    for ($i=0 ; $i < $itemQuantity ; $i++ ) {
      $this->goodItems[] = ['productNo' =>  $productNo,
      'productName' =>  $productName,
      'weightLine' =>  $weightLine,
      'amountLine' =>  $amountLine,
      'statisticalValueLine' => $statisticalValueLine,
      'itemQuantity' => 1,
      'itemType' => 0,
      'Length' =>  $Length,
      'Height' =>  $Height,
      'Width'=>$Width,
      'deliveryService' => false
    ];
  }
}


}
