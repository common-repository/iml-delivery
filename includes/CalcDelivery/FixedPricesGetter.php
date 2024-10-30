<?php
namespace Iml\CalcDelivery;

class FixedPricesGetter
{

  private $methodId;
  private $isCourierDelivery;

  public function __construct($cmsFacade, $methodId, $isCourierDelivery, $departureCity, $arrivalCity)
  {
    $this->methodId = $methodId;
    $this->departureCity = $departureCity;
    $this->arrivalCity = $arrivalCity;
    $this->cmsFacade = $cmsFacade;
    $this->isCourierDelivery = $isCourierDelivery;
  }



  
  public function getFixedResult()
  {
    if($this->isCourierDelivery)
    {
      if($this->departureCity == $this->arrivalCity)
      {
        return $this->cmsFacade->get_option('cdOwnRegionPrice');
      }else {
        return $this->cmsFacade->get_option('cdOtherRegionPrice');
      }
    }else {
      if($this->departureCity == $this->arrivalCity)
      {
        return $this->cmsFacade->get_option('pkOwnRegionPrice');
      }else {
        return $this->cmsFacade->get_option('pkOtherRegionPrice');
      }
    }
  }


}
