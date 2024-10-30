<?php

namespace Iml\CalcDelivery\Api;

/**
 * Разбор ответа от API
 */
class ApiResponse
{

    protected $httpcode;
    protected $response=false;
    protected $apiResponseFactory;

    const replaceSumParts = array('Комиссия с Суммы Прихода' => 'Комиссия за прием оплаты',
    'Комиссия с Оценочной Стоимости' => 'Страховой сбор');

    /**
     * ApiResponse constructor.
     * @param ApiErrorFactory $apiResponseFactory
     * @param null $httpcode
     * @param null $response
     */
    public function __construct(ApiErrorFactory $apiResponseFactory, $httpcode = null, $response = null)
    {
        $this->httpcode = $httpcode;
        $this->apiResponseFactory = $apiResponseFactory;
        if($response)
        {
            $this->response = json_decode($response, true);
        }
    }

    /**
     * @return float|int|void
     */
    public function getCalculationResult()
    {


        //веб-сервис может вообще не отвечать
        if(!$this->response && $this->httpcode)
        {
            $this->apiResponseFactory->throwErrorResponce('Ошибка вызова веб-сервиса. Попробуйте выполнить расчет через 20 минут.');
        }else
        {
            //разбираем ответ
            if($this->httpcode !=200)
            {
                switch ($this->httpcode) {
                    case 401 :
                    $this->apiResponseFactory->throwErrorResponce('Логин или пароль не верны');
                    break;
                    case 403 :
                    $this->apiResponseFactory->throwErrorResponce('Доступ запрещен');
                    break;
                    case 429 :
                    $this->apiResponseFactory->throwErrorResponce('Превышено ограничение по количеству запросов в минуту');
                    break;

                    default :
                        //если ошибка с объяснением
                    if ($this->response !== false && isset ($this->response ['Errors']) && is_array($this->response ['Errors'])) {
                        try
                        {
                            $error_params = array_shift($this->response ['Errors']);

                            $pos = strrpos($error_params['ErrorMessage'], ']');
                            $error_message=$error_params['ErrorMessage'];
                            if($pos!==false)
                            {

                                $error_message=substr($error_params['ErrorMessage'] ,$pos+1);
                            }
                            $error_message=(is_null($error_message)) ? '' : $error_message;
                            $console_info = sprintf('Ошибка вычисления. %s . Код ошибки - %s.', $error_params['ErrorMessage'], $error_params['Code']);
                        } catch (\Exception $e) {
                            $this->apiResponseFactory->throwErrorResponce('Ошибка разбора ответа от сервера');
                        }

                        $this->apiResponseFactory->throwErrorWithConsoleMessage(sprintf('Произошла ошибка. Текст ошибки:  %s', $error_message),
                            $console_info);
                    } else {
                        $this->apiResponseFactory->throwErrorResponce('Превышено время ожидания ответа. Попробуйте повторить запрос чуть позже.');
                    }
                }
            }elseif($this->httpcode==200 && $this->response !== false)
            {


                $deliveryCost = null;
                $deliveryDate = null;
                //получение стоимости
                try {
                    $priceType = $this->response ['PriceTypes'][0];
                    $deliveryCost = $priceType['DeliveryCost'];
                } catch (\Exception $e) {
                    $this->apiResponseFactory->throwErrorResponce('Ошибка разбора стоимости в ответе от сервера');
                }




                try {
                    $deliveryDate = \DateTime::createFromFormat (\DateTime::ATOM , $this->response ['DeliveryDate']);
                    $deliveryDate = $deliveryDate->format('d.m.Y');
                } catch (\Exception $e) {
                    $this->apiResponseFactory->throwErrorResponce('Ошибка разбора даты доставки в ответе от сервера');
                }

                $detailSumParts = array();
                try
                {

                    $priceType = $this->response ['PriceTypes'][0];
                    if(isset($priceType['AccountNoItems']))
                    {
                        $detailSumParts = $priceType['AccountNoItems'];
                    }

                } catch (\Exception $e) {
                    $this->apiResponseFactory->throwErrorResponce('Ошибка разбора детализации суммы в ответе от сервера');
                }

            // конвертируем расшифровку цены в корректный формат
                $convertedSumParts = array();
                $otherPartsSum = 0;

                foreach ($detailSumParts as $sumPart) {
                    if(array_key_exists($sumPart['Description'], self::replaceSumParts))
                        {
                            $part = $sumPart;
                            $part['Description'] = self::replaceSumParts[$sumPart['Description']];
                            $convertedSumParts[] = $part;
                            $otherPartsSum += round(floatval($sumPart['Cost']), 2);
                            $areOtherParts = 1;
                        }
                }

                if(isset($areOtherParts))
                {
                   $convertedSumParts[]  =   array (
                        'AccountNo' => ':7099',
                        'Description' => 'Доставка',
                        'Cost' => round(floatval($deliveryCost) - $otherPartsSum, 2)
                      );
                }


                return array_merge(compact('deliveryCost', 'deliveryDate'), array('detailSumParts' => $convertedSumParts));
            }
            else
            {
                $this->apiResponseFactory->throwErrorResponce('Сбой обращения к сервису');
            }

        }
    }
}
