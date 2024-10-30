<?php

namespace Iml\CalcDelivery\Api;

/**
 * Реализация запросов к API
 */
class ApiProvider
{

    private $apiResponseFactory;
    private $calcPriceApiUrl;
    private $interTimeout;

    public function __construct(ApiErrorFactory $apiResponseFactory, $calcPriceApiUrl, $interTimeout)
    {
        $this->apiResponseFactory = $apiResponseFactory;
        $this->calcPriceApiUrl = $calcPriceApiUrl;
        $this->interTimeout = $interTimeout;
    }

    /**
     * @param CalculationParameters $parameters
     * @return float
     * @throws ApiFailure
     */

    public function calculateDeliveryParams(CalculationParameters $parameters)
    {
    	try {
		    $response = wp_remote_post($this->calcPriceApiUrl, [
			    'body'        => $parameters->getJsonData(),
			    'timeout'     => $this->interTimeout,
			    'redirection' => 5,
			    'headers'     => [
				    'Authorization' => 'Basic ' . base64_encode($parameters->getLogin() . ':' . $parameters->getPassword())
			    ]
		    ]);

		    $apiResponse = $this->apiResponseFactory->getResponse($response['headers']['status'], wp_remote_retrieve_body($response));

	    } catch (\Exception $e) {
		    $this->apiResponseFactory->throwErrorResponce('Ошибка доступа к информационному веб-сервису');

		    $apiResponse = $this->apiResponseFactory->getResponse('500', 'Server Error');
	    }

	    return $apiResponse->getCalculationResult();
    }

}
