<?php


namespace Iml\Statuses;

class ApiResponse
{


  public function getResult($response)
  {

    $result = json_decode($response, true);
    if(empty($result))
    {
      throw new GetStatusError("Ответ от API является пустым", 1);
    }
    if(!is_array($result))
    {
      throw new GetStatusError("Некорректная структура данных в ответе", 1);
    }
    $result = array_shift($result);
    if(!isset($result['OrderStatus']))
    {
      throw new GetStatusError("В ответе отсутствует поле 'OrderStatus'", 1);
    }

    $newResult = ['OrderStatus' => $result['OrderStatus']];

    return $newResult;
  }

}
