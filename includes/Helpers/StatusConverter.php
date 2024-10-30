<?php

namespace Iml\Helpers;

class StatusConverter
{

    private $map = [
      1 => 'StsAccepted',
      2 => 'StsHand2Courier',
      10 => 'StsOnPickpont',
      27 => 'StsCanceled',
      3 =>  'StsDelivered'
  ];

  private $cmsFacade;

  public function __construct($cmsFacade)
  {
    $this->cmsFacade = $cmsFacade;
  }



  public function convert($order_id, $imlStatus)
  {
    if(array_key_exists($imlStatus, $this->map))
    {

      $wooStatus = $this->cmsFacade->get_option($this->map[$imlStatus]);
      if($wooStatus)
      {
        $order = new \WC_Order($order_id);
        if($order)
        {
          $order->update_status($wooStatus, 'изменено плагином IML');
          return true;
        }
      }

    }

    return false;
  }

}
