<?php
namespace Iml\CalcDelivery\Api;
use Iml\Helpers\CMSFacade;
class Factory
{

  private $cmsFacade;
  private $method;

  public function __construct($cmsFacade, $method = 'GetPlantCostOrder')
  {
    $this->cmsFacade = $cmsFacade;
    $this->method = $method;
  }

  public  function getService()
  {
    return new Service(
      new ApiProvider(
        new ApiErrorFactory($this->method),
        'http://api.iml.ru/v5one/'. $this->method,
        $this->cmsFacade->get_option('conIMLtimeout')
      ),
      new CalcParamsProcessing(
        new CalculationParametersFactory(), new AuthProvider($this->cmsFacade)
        )
      );
    }
  }
