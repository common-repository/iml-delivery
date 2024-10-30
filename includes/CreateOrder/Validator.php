<?php
namespace Iml\CreateOrder;

class Validator
{

  public function validate($params)
  {
    $needParams = [
      'RegionCodeTo',
      'RegionCodeFrom',
      'Job',
      'Amount',
      'Volume',
      'Weight',
      'Contact',
      'Phone',
      'Email',
      'Address',
      'GoodItems'
    ];

    foreach ($needParams as $value) {
      if(!isset($params[$value]))
      {
        throw new \Exception("При создании заказа отсутствует параметр {$value}", 1);
      }
    }

    return true;

  }

}
