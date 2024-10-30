<?php


namespace Iml\Statuses;

class Factory
{

  private $cmsFacade;
  public function __construct($cmsFacade)
  {
    $this->cmsFacade = $cmsFacade;
  }

  public function getService()
  {
    return new Service(new ApiProvider(
      $this->cmsFacade->get_option('iml-login'),
      $this->cmsFacade->get_option('iml-password'),
      5
    ));
  }

}
