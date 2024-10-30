<?php

namespace Iml\PrintBar;

class Service
{
  private $fileSaver;
  private $apiProvider;

  public function __construct($apiProvider, $fileSaver)
  {
    $this->apiProvider = $apiProvider;
    $this->fileSaver = $fileSaver;
  }

  public function getBarcodesFile($barcode)
  {
    if(!$barcode)
    {
      throw new \Exception("Не указан штрих-код заказа", 1);

    }
    $data = $this->apiProvider->getBarcodesFile($barcode);
    $this->fileSaver->save("{$barcode}.pdf", $data);

    return true;
  }


}
