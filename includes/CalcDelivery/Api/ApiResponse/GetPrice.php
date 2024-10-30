<?php
namespace Iml\CalcDelivery\Api\ApiResponse;

use Iml\CalcDelivery\Api\ApiResponse as BaseResponse;
use Iml\CalcDelivery\Api\ApiErrorFactory;

class GetPrice extends BaseResponse
{
    public function __construct(ApiErrorFactory $apiResponseFactory, $httpcode = null, $response = null)
    {
        parent::__construct($apiResponseFactory, $httpcode, $response);

        if ($this->response) {
            $this->response = [
                'Errors'       => $this->response['Errors'],
                'Status'       => $this->response['Status'],
                'DeliveryDate' => $this->response['DeliveryDate'],
                'PriceTypes'   => [
                    ['DeliveryCost' => $this->response['Price']]
                ],
            ];
        }
    }
}