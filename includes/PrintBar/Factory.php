<?php

namespace Iml\PrintBar;

class Factory
{

  private $cmsFacade;
  private $fileDataSaver;
  public function __construct($cmsFacade, $fileDataSaver)
  {
    $this->cmsFacade = $cmsFacade;
    $this->fileDataSaver = $fileDataSaver;
  }

  public function getService()
  {
    return new Service(new ApiProvider(
      $this->cmsFacade->get_option('iml-login'),
      $this->cmsFacade->get_option('iml-password')
    ), $this->fileDataSaver);
  }


}
