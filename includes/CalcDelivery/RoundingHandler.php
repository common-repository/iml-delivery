<?php

namespace Iml\CalcDelivery;

class RoundingHandler
{
	private $cmsFacade;
	public function __construct($cmsFacade)
	{
		$this->cmsFacade = $cmsFacade;
	}



	public function round($deliveryCost)
	{

		$roundingOper = $this->cmsFacade->get_option('roundingOper');
		$roundingMeasure = $this->cmsFacade->get_option('roundingMeasure');
		return $this->roundProcess($deliveryCost, $roundingOper, $roundingMeasure);
	}

	public function roundProcess($deliveryCost, $roundingOper, $roundingMeasure)
	{
		if(!$deliveryCost)
		{
			return $deliveryCost;
		}
		switch ($roundingMeasure) {
			case 'unit':
			$coef = 1;
			$mathPrecision = 0;
			break;
			case 'dozens':
			$coef = 10;	
			$mathPrecision = -1;			
			break;
			case 'hundreds':
			$coef = 100;				
			$mathPrecision = -2;			
			break;
		}


		switch ($roundingOper) {
			case 'no':
			return $deliveryCost;
			break;
			case '+':
			$deliveryCost = (int)floor($deliveryCost);
			$deliveryCost = $deliveryCost - ($deliveryCost % $coef) + $coef;
			break;
			case '-':
			$deliveryCost = (int)floor($deliveryCost);
			$deliveryCost = $deliveryCost - ($deliveryCost % $coef);
			break;
			case 'math':
			$deliveryCost = (int)round($deliveryCost, $mathPrecision);
			break;
		}

		return $deliveryCost;
	}


}