<?php
namespace Iml\CreateOrder;


class ApiResponse
{

  private $httpcode;
  private $response;

  public function __construct($response = null, $httpcode = null)
  {
    $this->httpcode = $httpcode;
    $this->response = json_decode($response, true);
  }


  public function getResult()
  {
    
    if($this->response["Result"] != "OK")
    {
      $allMesg  = '';
      if(isset($this->response["Errors"]))
      {
        foreach ($this->response["Errors"] as  $value) {
          $allMesg .= $value['Message'];
        }
      }else {
        $allMesg  = 'Произошла неизвестная ошибка при создании заказа';
      }

      throw new CreateOrderError($allMesg, 1);
    }

    if(!isset($this->response["Order"]))
    {
      throw new CreateOrderError('В ответе api отсутствует поле "Order"', 1);
    }

    if(!isset($this->response["Order"]['BarCode']))
    {
      throw new CreateOrderError('В ответе api отсутствует поле "BarCode"', 1);
    }


    return $this->response["Order"]['BarCode'];
  }


}
