<?php

namespace Iml\Helpers;

use Iml\ImlOrder\ConditionItemsHandler;


class CartInfo2OrderConverter
{

  private $cmsFacade;
  private $service;

  public function __construct($cmsFacade, $service)
  {
    $this->cmsFacade = $cmsFacade;
    $this->service = $service;
  }


 private function getCustomOrderNumber($order_id)
 {
  return $order_id ?: 'wp_'.time();
 }



  public function convert($params)
  {

    $imlOrder = $this->cmsFacade->get_post_meta( $params['order_id'], 'iml-ship-order-params', true );

    if(!$imlOrder)
    {
      throw new \Exception("Невозможно отправить заказ. Отсутствует информация по позициям заказа ", 1);
    }

    $imlOrder  = clone $imlOrder;
    if($this->cmsFacade->get_option('testMode')) 
    {
      $imlOrder->Test = 'True';
    }


    $imlOrder->volumeAr  = $params['place'];
    $imlOrder->keyTo = $params['keyTo'];
    $imlOrder->keyFrom = $params['keyFrom'];


    $imlOrder->enableFullfilment  = isset($params['enableFullfilment']);

    $placeManager = $this->service->getPlaceManager();

    $imlOrder->RegionCodeFrom = ($placeManager->getPlaceByKey($imlOrder->keyFrom))['regionCode'];
    $placeItemTo = $placeManager->getPlaceByKey($imlOrder->keyTo);
    $imlOrder->RegionCodeTo = $placeItemTo['regionCode'];

    $imlOrder->Job = $params['Job'];
    $imlOrder->DeliveryPoint = $params['DeliveryPoint'];
    $imlOrder->Amount = $params['Amount'];

    $imlOrder->ValuatedAmount = $params['ValuatedAmount'];
    $imlOrder->DeliveryCost = $params['DeliveryCost'];

    $imlOrder->Volume = count($params['place']);
    $imlOrder->Weight = $params['Weight'];

    $imlOrder->Contact = $params['Contact'];
    $imlOrder->Phone = $params['Phone'];
    $imlOrder->Email = $params['Email'];


    if(!$imlOrder->hasCourierService())
    {
      $imlOrder->Address = $params['Address'];
    }else {
      // добавим город из справочника для геоидентификации
      $imlOrder->Address = sprintf("%s, %s, %s, %s", $placeItemTo['region'], $placeItemTo['area'], $placeItemTo['city'], $params['Address']);
    }


    $imlOrder->Comment = $params['Comment'];

    $imlOrder->VAT = $params['VAT'];
    $imlOrder->DeliveryDate = $params['DeliveryDate'];


    $imlOrder->CustomerOrder = $this->getCustomOrderNumber($params['order_id']);


    $conditions = $this->service->getFullParcelConditionCollection();
    $conditionItemsHandler = new ConditionItemsHandler($imlOrder);
    $conditionItemsHandler->resetAllConditions();
    foreach ($conditions as $keyCond => $condition) {
      // по-умолчанию - разрешено
      $value = array_key_exists($keyCond, $params) && in_array($params[$keyCond], [ 1, 0]) ? $params[$keyCond] : 1;
      $conditionItemsHandler->setCondition($keyCond, $condition['Code'], ($value == 1));
    }

    return $imlOrder;

  }


}
