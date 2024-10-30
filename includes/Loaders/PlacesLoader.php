<?php

namespace Iml\Loaders;

abstract class PlacesLoader
{
  abstract protected function loadData();
  protected $dataSaver;
  public function __construct($dataSaver)
  {
    $this->dataSaver = $dataSaver;
  }

}
