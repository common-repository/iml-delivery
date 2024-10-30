<?php

namespace Iml\Loaders;

class FileDataSaver
{

  private $path;

  public function __construct($path)
  {
    $this->path = $path;
  }

  public function save($fileName, $data)
  {
    $pathFile = $this->path . '/' . $fileName;
    file_put_contents($pathFile, $data);
    return $pathFile;
  }

}
