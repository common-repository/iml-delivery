<?php
namespace Iml\CreateOrder;

class ApiProvider
{

  private $login;
  private $password;

  public function __construct($login, $password)
  {
    $this->login = $login;
    $this->password = $password;
  }


  public function createOrder($params)
  {
	  $response = wp_remote_post('http://api.iml.ru/Json/CreateOrder', [
		  'body'        => $params,
		  'timeout'     => 15,
		  'redirection' => 5,
		  'headers'     => [
			  'Authorization' => 'Basic ' . base64_encode($this->login .':'. $this->password)
		  ]
	  ]);

	  return new ApiResponse($response['headers']['status'], wp_remote_retrieve_body($response));
  }

}
