<?php

namespace Iml\CalcDelivery\Api;

class AuthProvider
{
    private $cmsFacade;

    public function __construct($cmsFacade) {
      $this->cmsFacade = $cmsFacade;

    }

    public function getAuthData(array $postParams): array
    {
        return array('login' => $this->cmsFacade->get_option('iml-login'), 'password' => $this->cmsFacade->get_option('iml-password'));
    }

}
