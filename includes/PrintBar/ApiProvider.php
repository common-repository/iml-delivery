<?php

namespace Iml\PrintBar;

class ApiProvider
{

  private $login;
  private $password;


  public function __construct($login, $password)
  {
    $this->login = $login;
    $this->password = $password;
  }



  public function getBarcodesFile($barcode)
  {
	  $response = wp_remote_get('http://api.iml.ru/Json/PrintBar?Barcode='. $barcode, [
		  'timeout'     => 15,
		  'redirection' => 5,
		  'headers'     => [
			  'Authorization' => 'Basic ' . base64_encode($this->login .':'. $this->password)
		  ]
	  ]);

	  return wp_remote_retrieve_body($response);
}

}
