<?php

namespace Iml\ImlOrder;

class GoodItemsHandler
{

  private $cmsFacade;
  private $order;

  const MAX_SIDE_SIZE = 150;
  const MAX_SIDES_SUM = 200;
  const MAX_PLACE_WEIGHT = 25;

  public function __construct($cmsFacade, $order)
  {
    $this->cmsFacade = $cmsFacade;
    $this->order = $order;
  }



  private function getWeightCoef()
  {
    $current_unit = strtolower($this->cmsFacade->get_option('woocommerce_weight_unit'));
    return ($current_unit == 'g') ? 1000 : 1;
  }

  private function getDimensionCoef()
  {
    $dimension_unit = strtolower($this->cmsFacade->get_option('woocommerce_dimension_unit'));
    $dimension_c    = 1;

    switch ($dimension_unit) {
      case 'm':
      $dimension_c = 100;
      break;
      case 'mm':
      $dimension_c = 0.1;
      break;
    }
    return $dimension_c;
  }


// фиксированная упаковка выбрана
  private function isFixPack()
  {
    $value = $this->cmsFacade->get_option('packageCalcType');
    if(!$value)
    {
      return true;
    }

    return ($value == 'fix-pack');

  }


  public function addGoods($items)
  {

  // проверка фикс упаковка или 
    $weight_c     = $this->getWeightCoef();
    $dimension_c = $this->getDimensionCoef();

    $extraWeight = (float) $this->cmsFacade->get_option('addExtraWeightKg');
    $extraWeight = ($extraWeight) ? $extraWeight : 0;

    $sumWeight = 0;

    $defaultLength = (float) $this->cmsFacade->get_option('defaultGoodLength');
    $defaultWidth = (float) $this->cmsFacade->get_option('defaultGoodWidth');
    $defaultHeight = (float) $this->cmsFacade->get_option('defaultGoodHeight');
    $defaultWeightKg = (float) $this->cmsFacade->get_option('defaultGoodWeightKg');

    $sumPlaceSide = 0;
    $sumPlaceWeight = 0;
    $this->order->Volume = 0;

    $fixUpak = $this->isFixPack();




    foreach ($items as $item) {
      $product    = new \WC_Product($item['product_id']);


      if($product->get_weight())
      {
      $itemWeight =  (float) $product->get_weight() * $weight_c; //вес товара в кг
    }else {
      $itemWeight = $defaultWeightKg;
    }



    $itemWeight += $extraWeight;

    if($product->get_height())
    {
      $itemHeight =  (float) $product->get_height() * $dimension_c;
    }else {
      $itemHeight = $defaultHeight;
    }


    if($product->get_width())
    {
      $itemWidth =  (float) $product->get_width() * $dimension_c;
    }else {
      $itemWidth = $defaultWidth;
    }


    if($product->get_length())
    {
      $itemLength =  (float) $product->get_length() * $dimension_c;
    }else {
      $itemLength = $defaultLength;
    }



    $quantity = $item['quantity'];
    $price = $product->get_price();
    $sumWeight += $itemWeight*$quantity;


    $this->order->addGoods(
      $product->get_sku(),
      $product->get_name(),
      $itemWeight,
      $price,
      $price,
      $quantity,
      $itemLength,
      $itemHeight,
      $itemWidth
    );
    
    
    if(!$fixUpak)
    {
      for ($i=0; $i < $quantity; $i++) { 
        $this->order->Volume++;
        $this->order->volumeAr[] = [
          'Weight'  =>  $itemWeight,
          'Length'  =>  $itemLength,
          'Width'  =>   $itemWidth,
          'Height'  =>  $itemHeight    
        ];          
      }
    }
  }
  
  
  if($fixUpak)
  {

    // расчет мест отменен - все по товарам
    $Volume = $this->cmsFacade->get_option('defaultPlacesCount');
    $this->order->Volume = ($Volume) ? $Volume : 1;
    $this->order->volumeAr = [];  
    for ($i=0; $i < $Volume; $i++) {
      $this->order->volumeAr[] =
      [
        'Weight'  =>  (float)$this->cmsFacade->get_option('defaultWeightKg') + $extraWeight,
        'Length'  =>  (int)$this->cmsFacade->get_option('defaultLength'),
        'Width'  =>  (int)$this->cmsFacade->get_option('defaultWidth'),
        'Height'  =>  (int)$this->cmsFacade->get_option('defaultHeight')
      ];
    }
  }
}



private function getVolumeAr($Volume)
{

  return $result;
}





}
