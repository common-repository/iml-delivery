<?php
namespace Iml\CalcDelivery;

class DeliveryResultsCorrector
{
  private $cmsFacade;
  public function __construct($cmsFacade)
  {
    $this->cmsFacade = $cmsFacade;
  }

  public function correct($data)
  {

    $dpExtraNumber = (float)$this->cmsFacade->get_option('dpExtraNumber');
    if($dpExtraNumber)
    {
      switch ($this->cmsFacade->get_option('dpExtraOper')) {
        case '-':
        $data['deliveryCost']  = ($this->cmsFacade->get_option('dpExtraMeasure') == 'руб') ?
        (float) $data['deliveryCost'] - $dpExtraNumber            :
        (float) $data['deliveryCost'] - ((float) $data['deliveryCost'] * $dpExtraNumber/100);
        break;
        case '+':
        $data['deliveryCost']  = ($this->cmsFacade->get_option('dpExtraMeasure') == 'руб') ?
        (float) $data['deliveryCost'] + $dpExtraNumber            :
        (float) $data['deliveryCost'] + ((float) $data['deliveryCost'] * $dpExtraNumber/100);
        break;
      }

      if($data['deliveryCost'] < 0 )
      {
        $data['deliveryCost']  = 0;
      }
    }


    if(!$this->cmsFacade->get_option('showDateDelivery'))
    {
      $data['deliveryDate'] = '';
    }else {
      $addDeliveryDateDays = (int) $this->cmsFacade->get_option('addDeliveryDateDays');
      if($addDeliveryDateDays && isset($data['deliveryDate']))
      {
        $deliveryDate = \DateTime::createFromFormat('d.m.Y', $data['deliveryDate']);
        if($deliveryDate)
        {
          $deliveryDate->add(new \DateInterval(sprintf('P%sD', $addDeliveryDateDays)));
          $data['deliveryDate'] = $deliveryDate->format('d.m.Y');
        }else
        {
          $data['deliveryDate'] = ' - ';
        }
      }
    }

    return $data;
  }

}
