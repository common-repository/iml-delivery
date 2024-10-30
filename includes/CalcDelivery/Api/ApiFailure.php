<?php

namespace Iml\CalcDelivery\Api;

/**
 * Ошибка при проблеме обработки запроса в API
 */
class ApiFailure extends \Exception
{

    private $errorMessage;
    private $consoleMessage;


    public function __construct($errorMessage = null, $consoleMessage = null)
    {
        $this->errorMessage = $errorMessage;
        $this->consoleMessage = $consoleMessage;

    }


    /**
     * @return string
     */
    public function getErrorText()
    {
        return $this->errorMessage;
    }

    /**
     * @return bool
     */
    public function hasConsoleInfo()
    {
        return !is_null($this->consoleMessage);
    }

    /**
     * @return string
     */
    public function getConsoleInfo()
    {
        return $this->consoleMessage;
    }

}
