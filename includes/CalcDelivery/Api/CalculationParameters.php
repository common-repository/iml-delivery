<?php

namespace Iml\CalcDelivery\Api;

/**
 * Объект с методами получения структуры данных для запроса к API
 */
class CalculationParameters
{
    private $data;
    private $post;
    private $authData;

    /**
     * CalculationParameters constructor.
     * @param array $data
     * @param array $post
     * @param array $authData
     */
    public function __construct(array $data, array $post, array $authData)
    {
        $this->data     = $data;
        $this->post     = $post;
        $this->authData = $authData;
    }

    /**
     * @return string
     */
    public function getJsonData()
    {
        return json_encode($this->data);
    }

    /**
     * @return array
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->authData['login'];
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->authData['password'];
    }
}
