<?php
namespace Iml\Helpers;

class StatusManager
{

  private $statusList;
  public function __construct($statusList)
  {
    $this->statusList = $statusList;
  }


  public function getStatusByCode($code)
  {
    return isset($this->statusList[$code]) ? $this->statusList[$code]['Name'] : false;
  }


}
