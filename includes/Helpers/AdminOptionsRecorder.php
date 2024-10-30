<?php

namespace Iml\Helpers;

class AdminOptionsRecorder
{
  private $cmsFacade;
  public function __construct($cmsFacade)
  {
    $this->cmsFacade = $cmsFacade;
  }

  public function handle($item)
  {
    $name = sprintf("cndPrc3_%s", $item['Code']);
    if($this->cmsFacade->get_option($name) === false)
    {
      $this->cmsFacade->add_option($name, '0');
    }
    return $name;
  }

}
