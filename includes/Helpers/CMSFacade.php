<?php
namespace Iml\Helpers;

class CMSFacade
{
  public  function __call($name, $arguments) {
      return call_user_func_array($name, $arguments);
   }


}
