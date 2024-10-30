<?php
namespace Iml;


class ImlAjaxHandler
{

  private $placeManager;
  private $pvzManager;
  private $service;
  private $orderHandler;
  private $printBarFactory;
  private $statusConverter;
  private $calcDeliveryFactory;
  private $cartInfo2OrderConverter;


  public function __construct($service, $placeManager, $pvzManager, $orderHandler, $printBarFactory, $statusConverter, $calcDeliveryFactory, $cartInfo2OrderConverter)
  {
    $this->service = $service;
    $this->placeManager = $placeManager;
    $this->pvzManager = $pvzManager;
    $this->orderHandler = $orderHandler;
    $this->printBarFactory = $printBarFactory;
    $this->statusConverter = $statusConverter;
    $this->calcDeliveryFactory = $calcDeliveryFactory;
    $this->cartInfo2OrderConverter = $cartInfo2OrderConverter;
  }

  public function loadImlRegions()
  {
    try {
    	$this->service->prepareCollections();
    } catch (\Exception $e) {
      echo 0;
      wp_die();
    }
    echo esc_html(get_option('lastUpdateLists'));
  	wp_die();
  }



  public function deliveryPvzTypeChanged($old_value, $value )
  {
// изменение опции - доставлять только на ПВЗ или доставлять на ПВЗ и постаматы
    $this->service->prepareCollections();
  }




  public function  getPlaceKeyByPvzID()
  {
    if(isset($_POST['ID']))
    {
      $pvzItem = $this->pvzManager->getPvzByID(sanitize_text_field($_POST['ID']));
      if($pvzItem)
      {
        echo json_encode(['result' => $pvzItem['key']], JSON_UNESCAPED_UNICODE);
      }else {
        echo json_encode(['error' => 'пвз не найден в справочнике'], JSON_UNESCAPED_UNICODE);
      }

    }
    wp_die();
  }

  public function getPlacesByJob()
  {
    if(isset($_POST['Job']))
    {
      $placesList = $this->placeManager->getPlacesByJob(sanitize_text_field($_POST['Job']));
      if($placesList)
      {
        echo json_encode($placesList, JSON_UNESCAPED_UNICODE);
      }else {
        echo 0;
      }

    }
    wp_die();
  }


  public function getPvzByPlaceKey()
  {
    if(isset($_POST['Job']) &&  isset($_POST['placeKey']))
    {
      $pvzList = $this->pvzManager->getPvzListByKey(sanitize_text_field($_POST['placeKey']), $_POST['Job'] === 'С24КО');
      if($pvzList)
      {
        echo json_encode($pvzList, JSON_UNESCAPED_UNICODE);
      }else {
        echo 0;
      }

    }
    wp_die();
  }


  public function updateImlRequestStatus()
  {
    if(isset($_POST['imlBarCode']) && isset($_POST['order_id']))
    {
    try
      {
        $result = $this->orderHandler->requestImlStatus(sanitize_text_field($_POST['imlBarCode']));
        $this->orderHandler->saveOrderProperty(sanitize_text_field($_POST['order_id']), 'imlStatus', $result['OrderStatus']);
        $this->statusConverter->convert(sanitize_text_field($_POST['order_id']), $result['OrderStatus']);
      } catch (\Exception $e)
      {
        ___p($e->getMessage());
        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        wp_die();
      }
      echo json_encode(['result' => 'ok'], JSON_UNESCAPED_UNICODE);
    }
    wp_die();
  }



  public function printBar4Order()
  {
    if(isset($_POST['barcode']))
    {
    try
      {
        ($this->printBarFactory->getService())->getBarcodesFile(sanitize_text_field($_POST['barcode']));

      } catch (\Exception $e)
      {
        ___p($e->getMessage());
        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        wp_die();
      }
      echo json_encode(
        ['result' => 'ok',
        'url' => admin_url( 'options-general.php?page=print-iml-barcode&barcode='.sanitize_text_field($_POST['barcode']))
        ], JSON_UNESCAPED_UNICODE);
    }
    wp_die();


  }

  public function showPvzSelector()
  {
      $chosenShippingID = $this->service->getChosenShippingID();
      $deliveryInPVZOnly = get_option('deliveryInPVZOnly');

      // // var_dump($chosenShippingID);
      if((!is_checkout() && in_array($chosenShippingID, ['iml_method_c24ko', 'iml_method_с24']))
      || is_checkout()
      )
      {
        $city = WC()->customer->get_shipping_city();
        $region = WC()->customer->get_shipping_state();

        include_once dirname( __FILE__ ) . "/Views/pvz_selector.php";
      }

  }


  public function setSelectedPvz()
  {

    if(isset($_POST['address']))
    {
      // session_start();
      $_SESSION['iml-selected-pvz'] = sanitize_text_field($_POST['address']);
      $_SESSION['iml-pvz-ID'] = sanitize_text_field($_POST['ID']);

      $_SESSION['iml-pvz-RequestCode'] = sanitize_text_field($_POST['RequestCode']);
      $_SESSION['iml-pvz-Special_Code'] = sanitize_text_field($_POST['Special_Code']);
      $_SESSION['iml-pvz-RegionCode'] = sanitize_text_field($_POST['RegionCode']);
      $_SESSION['iml-pvz-City'] = sanitize_text_field($_POST['City']);
      $_SESSION['iml-pvz-Region'] = sanitize_text_field($_POST['Region']);

      WC()->customer->set_shipping_city(sanitize_text_field($_POST['City']));
      WC()->customer->set_shipping_state(sanitize_text_field($_POST['Region']));
      WC()->customer->set_billing_city(sanitize_text_field($_POST['City']));
      WC()->customer->set_billing_state(sanitize_text_field($_POST['Region']));
      WC()->customer->set_billing_address_1(sanitize_text_field($_POST['address']));
      WC()->customer->set_shipping_address_1(sanitize_text_field($_POST['address']));
      WC()->customer->set_shipping_postcode('');
      WC()->customer->set_billing_postcode('');

      echo 1;
    }else {
      echo 0;
    }
    wp_die();
  }


  public function recalcDeliveryCost()
  {
    if(isset($_POST['formData']))
    {
      parse_str($_POST['formData'], $formData);
      $formData = array_map('sanitize_text_field', $formData);

      // ___p($formData);
      try
        {
          $imlOrder = $this->cartInfo2OrderConverter->convert($formData);
          $apiParams = (new \Iml\ImlOrder\CalcDeliveryProxy($imlOrder))->getData();
          // ___p($apiParams);
          // die();
          $service = $this->calcDeliveryFactory->getService();
          $deliveryConditions = $service->calculateDeliveryConditions($apiParams);

          echo json_encode(['cost' => $deliveryConditions['deliveryCost'], 'date' => $deliveryConditions['deliveryDate']]);
      }
      catch (ApiFailure $apiFailure) {
        echo json_encode(['error' => $apiFailure->getErrorText(), 'console' => $apiFailure->getConsoleInfo()]);
      } catch (\Exception $exception) {
        ___p($e->getMessage());
        echo json_encode(['error' => $exception->getMessage(), 'console' => '']);
      }
  }
    wp_die();
  }




}
