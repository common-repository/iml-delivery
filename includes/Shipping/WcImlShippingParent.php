<?php

namespace Iml\Shipping;
use Iml\Helpers\CMSFacade;
use Iml\CalcDelivery\FixedPricesGetter;
use Iml\CalcDelivery\DeliveryResultsCorrector;
use Iml\ImlOrder\Order;
use Iml\ImlOrder\GoodItemsHandler;
use Iml\ImlOrder\ConditionItemsHandler;
use Iml\ImlOrder\CalcDeliveryProxy;
use Iml\CalcDelivery\Api\ApiFailure;
use Iml\Service;
use Iml\Helpers\Logger;
use Iml\CalcDelivery\RoundingHandler;



class WcImlShippingParent extends \WC_Shipping_Method
{

  public $Job;
  public $delivery;
  public $priceWhenFailCon;
  public $isCourierDelivery;
  public $isCashService;



  public function __construct($instance_id = 0)
  {
    parent::__construct();
    $this->instance_id = absint($instance_id);
    $this->supports    = array(
      'shipping-zones',
      'instance-settings',
    );
    $this->init_form_fields();
    $this->init_settings();

    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
  }




  final public function calculate_shipping($package = array())
  {

    if (!is_cart() && !is_checkout()) {
      return false;
    }



    $chosen_payment_method = WC()->session->get('chosen_payment_method');
    // если оплата при доставке, а у услуги нет КО
    if($chosen_payment_method == 'cod' && !$this->isCashService)
    {
      return false;
    }

    $cmsFacade = new CMSFacade();
    $pluginService = Service::getInstance();
    $placeManager = $pluginService->getPlaceManager();


    $departureCity = get_option('departureCity');
    // ___p($departureCity);
    if(!$departureCity)
    {
      return false;
    }


    $placeItemFrom = $placeManager->getPlaceByKey($departureCity);
    if(!$placeItemFrom)
    {
      return false;
    }

    // на основе ввода пользователя находим наш нас. пункт
    $placeItemTo = $placeManager->find($package['destination']['city'], $package['destination']['state'], $this->Job);
    // ___p($placeItemTo);

    if(!$placeItemTo)
    {
      $logger = Logger::getInstance();
      $logger->notice('в справочнике не найден населенный пункт', $package['destination']);
      return false;
    }

    $imlOrder = new Order();
    $imlOrder->RegionCodeFrom = $placeItemFrom['regionCode'];
    $imlOrder->RegionCodeTo = $placeItemTo['regionCode'];
    $imlOrder->DeliveryPoint = 0;


      // расчет доставки до ПВЗ и ПВЗ выбран через виджет
      if(!$this->isCourierDelivery)
      {
        if(isset($_SESSION['iml-pvz-RequestCode']))
        {
          $imlOrder->DeliveryPoint = sanitize_text_field($_SESSION['iml-pvz-RequestCode']);
          // ___p('a');
        }else {
          $imlOrder->DeliveryPoint = $placeItemTo['RequestCode'];
          // ___p('b');
        }
      }


    // ___p($imlOrder);
    global $woocommerce;
    $cart_products = $woocommerce->cart->get_cart();
    // получим GoodItems, Weight, Volume на основе списка товаров из корзины
    (new GoodItemsHandler($cmsFacade, $imlOrder))->addGoods($cart_products);
    // ___p($imlOrder);
    

    // укажем услуги, настроенные в админке
    $this->setDefaultOrderConditons($imlOrder, $pluginService->getFullParcelConditionCollection());


    $imlOrder->Job = $this->Job;
    $imlOrder->DetailedResult = false;
    // $paymentType = 1;
    $delivery = $this->delivery;

    $imlOrder->Address =   sprintf("%s, %s, %s", $placeItemTo['region'], $placeItemTo['area'], $placeItemTo['city']);
    $imlOrder->isCashService = $this->isCashService;


    $imlOrder->Amount = $woocommerce->cart->cart_contents_total + $woocommerce->cart->tax_total;

    $imlOrder->ValuatedAmount = 0;
    if(get_option('enableValuatedAmount'))
    {
      $imlOrder->ValuatedAmount = $woocommerce->cart->cart_contents_total + $woocommerce->cart->tax_total;
    }
    $label = '';
    $cost = '';

//___p(spl_autoload_functions ());
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
    $imlOrder->isCourierDelivery = $this->isCourierDelivery;
    $isException = false;
    try {

        $deliveryDate = null;
        // если цены на доставку зафиксированы
        if(get_option('calcType') == 'Таблица')
        {
          $fixedPricesGetter = new FixedPricesGetter($cmsFacade, $this->id, $this->isCourierDelivery, $imlOrder->RegionCodeFrom , $imlOrder->RegionCodeTo);
          $cost = $fixedPricesGetter->getFixedResult();
          $label = $this->title;
        }else
        {
          $method = get_option('iml_calc_method');
          if (!in_array($method, ['GetPlantCostOrder', 'GetPrice'])) $method = 'GetPlantCostOrder';

          $apiParams = (new CalcDeliveryProxy($imlOrder, $method))->getData();
          // ___p($apiParams);
          $service = (new \Iml\CalcDelivery\Api\Factory($cmsFacade, $method))->getService();
          $deliveryConditions = $service->calculateDeliveryConditions($apiParams);

          // корректируем результаты в соответствии с заданными правилами
          $deliveryResultsCorrector = new DeliveryResultsCorrector($cmsFacade);
          $deliveryConditions = $deliveryResultsCorrector->correct($deliveryConditions);
          $deliveryDate = $deliveryConditions['deliveryDate'];
          $label = $this->title . $deliveryDate;
          $cost = $deliveryConditions['deliveryCost'];
          // округляем результаты в соответствии с правилами
          $roundingHandler = new RoundingHandler($cmsFacade);
          $cost = $roundingHandler->round($cost);
        }


    } catch (ApiFailure $apiFailure) {
  		___p(array('ошибка запроса стоимости доставки ', $apiFailure->getErrorText(), $apiFailure->getConsoleInfo(), $imlOrder));
      if(strpos($apiFailure->getErrorText(), 'не доступна') != false)
      {
        $label = $this->title. ': не доступна на данном ПВЗ';
        $cost  = '-';

      }else {
        $isException = true;
      }
    } catch (\Exception $exception) {
      $isException = true;
      ___p($exception->getMessage());
    }


    if($isException)
    {

      if(get_option('showIMLDelWhenFailCon'))
      {
        $label = $this->title;
        $cost = $this->priceWhenFailCon;
      }
    }else {

      if($this->id == $pluginService->getChosenShippingID() && is_checkout())
      {

        $imlOrder->DeliveryDate      = sanitize_text_field($deliveryDate);
        $imlOrder->DeliveryCost      = sanitize_text_field($cost);
        $imlOrder->keyFrom           = sanitize_text_field($departureCity);
        $imlOrder->keyTo             = sanitize_text_field($placeItemTo['key']);
        $imlOrder->isCourierDelivery = sanitize_text_field($this->isCourierDelivery);
        $imlOrder->VAT               = sanitize_text_field($pluginService->getOption('request_settings', 'defaultVAT'));
        $imlOrder->addressPVZ        = (!$this->isCourierDelivery && isset($_SESSION['iml-selected-pvz'])) ? sanitize_text_field($_SESSION['iml-selected-pvz']) : '';
        $imlOrder->serviceName       = sanitize_text_field($this->title);
        $imlOrder->enableFullfilment = sanitize_text_field(get_option('enableFullfilment'));
        $_SESSION['iml-ship-order-params'] = $imlOrder;
        // ___p($imlOrder);
      }

    }

    if($label && $cost)
    {
      $this->addRate($this->get_rate_id(), $label, $cost);
    }

    return false;
  }



  private function addRate($id, $label, $cost)
  {
    $this->add_rate(array(
      'id'    => $id,
      'label' => $label,
      'cost'  => $cost
    ));
  }


  private function setDefaultOrderConditons($order, $conditions)
  {
    $conditionItemsHandler = new ConditionItemsHandler($order);
    foreach ($conditions as $keyCond => $condition) {
      // по-умолчанию - все опции разрешены
      $value = is_null(get_option($keyCond)) || !in_array(get_option($keyCond), [0, 1]) ? 1 : get_option($keyCond);
      $conditionItemsHandler->setCondition($keyCond, $condition['Code'], ( $value == 1));
    }
  }




}
