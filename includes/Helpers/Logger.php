<?php


namespace Iml\Helpers;

class Logger
{

  private static $instance = null;
  private $logDirectory;
  private $fileHandle;
  private $logFilePath;

  public function __construct($logDirectory)
  {
    $this->logDirectory = $logDirectory;
    $this->logFilePath = $this->getLogFilePath();

    if(file_exists($this->logFilePath) && !is_writable($this->logFilePath)) {
      throw new \Exception('Ошибка записи в файл логов. Проверьте права доступа к файлу');
    }
    $this->setFileHandle('a');

    self::$instance = $this;
  }


  private function getLogFilePath()
  {
    $ts = $this->getTimestamp('d_m_Y');
    return $this->logDirectory.'/'.$ts.'.log';
  }

  public static function getInstance()
  {
    return  self::$instance;
  }


  public function setFileHandle($writeMode)
  {
    $this->fileHandle = fopen($this->logFilePath, $writeMode);
  }

  public function notice($message, $context = '')
  {
    $this->writeMessage('notice', $message, $context);
  }

  public function debug($message, $context = '')
  {
    $this->writeMessage('debug', $message, $context);
  }

  public function error($message, $context = '')
  {
    $this->writeMessage('error', $message, $context);
  }


  private function write($message)
  {
    if (null !== $this->fileHandle) {
      if (fwrite($this->fileHandle, $message) === false) {
        throw new \Exception('Ошибка записи в файл логов. Проверьте права доступа к файлу');
      }
    }
  }

  private function writeMessage($level, $message, $context)
  {

    $message = $this->formatMessage($level, $message, $context);
    $this->write($message);
  }


  private function formatMessage($level, $message, $context)
  {
    $parts = array(
      'date'          => $this->getTimestamp(),
      'level'         => strtoupper($level),
      'message'       => $message,
      'context'       => json_encode($context, JSON_UNESCAPED_UNICODE)
    );
    foreach ($parts as $key => $value) {
      $parts[$key] = "[{$value}]";
    }
    return implode (' ',  $parts).PHP_EOL;
  }



  private function getTimestamp($default = 'H:i:s d.m.Y')
  {

    $originalTime = microtime(true);
    $micro = sprintf("%06d", ($originalTime - floor($originalTime)) * 1000000);
    $date = new \DateTime(date('Y-m-d H:i:s.'.$micro, $originalTime));
    return $date->format($default);
  }


}
