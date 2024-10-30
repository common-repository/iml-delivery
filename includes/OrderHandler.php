<?php

namespace Iml;

use Iml\ImlOrder\CreateOrderProxy;
use Iml\Service;
use Iml\CreateOrder\CreateOrderError;
use Iml\ImlOrder\GoodItemsHandler;

class OrderHandler
{

  private $createOrderFactory;
  private $cartInfo2OrderConverter;
  private $getStatusFactory;
  private $statusManager;
  private $imlService;
  private $orderStatusConverter;
  private $cmsFacade;

  public function __construct($cartInfo2OrderConverter, $createOrderFactory, $getStatusFactory, $statusManager, $imlService, $orderStatusConverter, $cmsFacade)
  {
    $this->cartInfo2OrderConverter = $cartInfo2OrderConverter;
    $this->createOrderFactory = $createOrderFactory;
    $this->getStatusFactory = $getStatusFactory;
    $this->statusManager = $statusManager;
    $this->imlService = $imlService;
    $this->orderStatusConverter = $orderStatusConverter;
    $this->cmsFacade = $cmsFacade;
  }




  public function isExistsImlOrder($order_id)
  {
    $imlOrder = $this->getImlOrderByID($order_id);
    return !empty($imlOrder);
  }

  public function hasCourierService($order_id)
  {
    $imlOrder = $this->getImlOrderByID($order_id);
    return $imlOrder->hasCourierService();
  }

  public function display4Admin($order)
  {
    $order_id = $order->get_id();
    if($this->isExistsImlOrder($order_id))
    {
      include_once dirname( __FILE__ ) . "/Views/Order/admin.php";
    }
  }

  public function display4User($order_id)
  {
    $order = wc_get_order($order_id);
    $shipping_method = @array_shift($order->get_shipping_methods());
    $shipping_method_id = $shipping_method['method_id'];
    if(
      $this->imlService->isImlShippingMethod($shipping_method_id) &&
      $this->isExistsImlOrder($order_id) &&
      !$this->hasCourierService($order_id)
    )
    {
      include_once dirname( __FILE__ ) . "/Views/Order/user.php";
    }
  }


  public function clearPVZCode($address)
  {
    unset($_SESSION['iml-selected-pvz']);
    unset($_SESSION['iml-selected-pvz-id']);
    unset($_SESSION['iml-pvz-ID']);
    unset($_SESSION['iml-pvz-RequestCode']);
    unset($_SESSION['iml-pvz-Special_Code'] );
    unset($_SESSION['iml-pvz-RegionCode']);
    unset($_SESSION['iml-pvz-City']);
    unset($_SESSION['iml-pvz-Region']);
    return $address;
  }



  // сохраняем данные при формировании заказа в дополнительных полях заказа
  public function savePvz2OrderField($order_id )
  {
    $order = new \WC_Order( $order_id );

    foreach ($order->get_shipping_methods() as $shipping_method) {
      $meta_data = $shipping_method->get_meta_data();

      foreach ($meta_data as $data) {
        $data = $data->get_data();

        if ($data['key'] == 'imlOrder') {
          update_post_meta($order_id, 'iml-ship-order-params', sanitize_iml_object($data['value']));
        }
      }
    }

    // don't forget appropriate sanitization if you are using a different field type
    if( isset($_SESSION['iml-selected-pvz']) ) {
      update_post_meta( $order_id, '_iml_selected_pvz_field', sanitize_text_field($_SESSION['iml-selected-pvz'] ) );
    }

    if( isset($_SESSION['iml-selected-pvz-id']) ) {
      update_post_meta( $order_id, '_iml_selected_pvz_id_field', sanitize_text_field($_SESSION['iml-selected-pvz-id'] ) );
    }

    if(isset($_SESSION['iml-ship-order-params'])) {
      update_post_meta( $order_id, 'iml-ship-order-params', sanitize_iml_object($_SESSION['iml-ship-order-params']) );
    }
  }

  public function saveExtraDetails($post_id, $post )
  {
    update_post_meta( $post_id, '_iml_selected_pvz_field', wc_clean( $_POST[ '_iml_selected_pvz_field' ] ) );
  }

  // показываем столбцы IML в таблице заказов в  админке
  public function adminOrderColumns($columns)
  {
    $new_columns = ( is_array( $columns ) ) ? $columns : array();
    $new_columns['MY_COLUMN_ID_1'] = 'Статус IML заявки';
    $new_columns['MY_COLUMN_ID_2'] = 'IML штрих-код';
    $new_columns['MY_COLUMN_ID_3'] = 'Действия';
    return $new_columns;
  }


  // показываем кнопки отправки заявок на создание заказа в IML
  public function adminOrderValues($column)
  {
    global $post;
    $order_id = $post->ID;
    if(!empty(get_post_meta( $post->ID , 'iml-ship-order-params', true )))
    {
      try {
        switch ($column) {
          case 'MY_COLUMN_ID_1':
          $imlOrder = get_post_meta( $post->ID , 'iml-ship-order-params', true );
          echo isset($imlOrder->imlStatus) ? esc_html($this->getOrderStatusTitle($imlOrder->imlStatus)) : '';
          break;

          case 'MY_COLUMN_ID_2':
          $imlOrder = get_post_meta( $post->ID , 'iml-ship-order-params', true );
          echo isset($imlOrder->imlBarcode) ? esc_html($imlOrder->imlBarcode) : '';
          break;

          case 'MY_COLUMN_ID_3':
          include dirname( __FILE__ ) . "/Views/Order/btn_request.php";
          if(isset($_GET['debug']))
          {
            var_dump(get_post_meta( $order_id, 'iml-ship-order-params', true ));
          }
          break;
        }

      } catch (\Exception $e) {
          ___p($e->getMessage());
      }

    }
  }



  public function getOrderStatusTitle($code)
  {
    // $code = 7;
    $statusName = $this->statusManager->getStatusByCode($code);
    return ($statusName) ? $statusName : 'статус не определен';
  }


  public function getImlOrderByID($order_id)
  {
    return get_post_meta( $order_id, 'iml-ship-order-params', true );
  }


  public function savedOrderItems($order_id)
  {
    $imlOrder  = $this->getImlOrderByID($order_id);
    if(!$imlOrder)
    {
      return;
    }


    // заявка отправлена - обновлять невозможно
    if(!empty($imlOrder->imlBarcode))
    {
      return;
    }
    $order = wc_get_order( $order_id );

    $items = $order->get_items();


    $products = [];
    foreach ($items as $item) {
      $products[] = ['product_id' => $item->get_product_id(), 'quantity' => $item->get_quantity()];
    }

    $imlOrder->Amount = (float)$order->total - (float)$order->shipping_total;

    $imlOrder->ValuatedAmount = 0;
    if(get_option('enableValuatedAmount'))
    {
      $imlOrder->ValuatedAmount = $imlOrder->Amount;
    }

    $imlOrder->deleteGoods();
    (new GoodItemsHandler($this->cmsFacade, $imlOrder))->addGoods($products);

    update_post_meta( $order_id, 'iml-ship-order-params', sanitize_iml_object($imlOrder));
// ___p($imlOrder);
// die();
  }


  public function saveOrderProperty($order_id, $properyName, $propertyValue)
  {
    $imlOrder = $this->getImlOrderByID($order_id);
    $imlOrder->$properyName = $propertyValue;

    update_post_meta( $order_id, 'iml-ship-order-params', sanitize_iml_object($imlOrder));
  }

  public function requestImlStatus($imlBarCode)
  {
    if(!$imlBarCode)
    {
      throw new \Exception("У заказа отсутствует штрих-код", 1);
    }
    $service = $this->getStatusFactory->getService();
    $params = ['BarCode' => $imlBarCode];
    // var_dump($params);
    $results = $service->getStatuses($params);
    // ___p($results);
    return $results;
  }


  public function scheduleImlRequestStatuses()
  {

    // проверяем статусы для заказов с IML за последние 30 дней
    define('IML_PROCESSING_INTERVAL', 86400*30);

    $args = array(
        'status' => ['pending', 'processing', 'on-hold', 'refunded'],
        'date_created' => '>' . ( time() - IML_PROCESSING_INTERVAL ),
        'orderby' => 'date_created',
        'order' => 'DESC'
    );
    $orders = wc_get_orders( $args );
    foreach ($orders as $order)
    {
      $shipping_method = @array_shift($order->get_shipping_methods());
      $shipping_method_id = $shipping_method['method_id'];
      // ___p($shipping_method_id);
      if($this->imlService->isImlShippingMethod($shipping_method_id))
      {
        $imlOrder = get_post_meta( $order->get_id() , 'iml-ship-order-params', true );
        if($imlOrder && isset($imlOrder->imlBarcode))
        {
          // ___p($imlOrder->imlBarcode);
          $result = $this->requestImlStatus($imlOrder->imlBarcode);
          // ___p($result);
          $this->saveOrderProperty($order->get_id(), 'imlStatus', $result['OrderStatus']);
          $this->orderStatusConverter->convert($order->get_id(), $result['OrderStatus']);
        }
      }

    }

  }



  public function createPageIMLRequest(){
    // http://localhost:8100/wp-admin/options-general.php?page=create-order-iml
    add_submenu_page(null, 'Заявка для заказа IML', 'Заявка для заказа IML', 'manage_options', 'create-order-iml', array($this, 'showPageIMLRequest'));
    add_submenu_page(null, 'Печать штрих-кодов для  IML заказа', 'Печать штрих-кодов для  IML заказа', 'manage_options', 'print-iml-barcode', array($this, 'printBarIMLRequest'));
  }


  public function printBarIMLRequest()
  {
    if(isset($_GET['barcode']))
    {
      $filePath = $this->imlService->getCollectionsPath() ."/{$_GET['barcode']}.pdf";
      if(file_exists($filePath))
      {
        header('Content-Description: File Transfer');
        header('Content-Type:  application/pdf');
        header("Content-disposition: inline; filename={$_GET['barcode']}.pdf");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        ob_clean();
        flush();
        readfile($filePath);
        exit;
      }
    }
  }


  public function showPageIMLRequest()
  {

    if(isset($_POST['order_id']))
    {
      try {
        $imlOrder = $this->cartInfo2OrderConverter->convert($_POST);

        // перезаписываем структуру заказа
        update_post_meta( sanitize_text_field($_POST['order_id']), 'iml-ship-order-params', sanitize_iml_object($imlOrder));
        $params = (new CreateOrderProxy($imlOrder))->getData();
        $service = $this->createOrderFactory->getService();

        // ___p($params);
        // die();
        $imlOrder->imlBarcode = $service->createOrder($params);
        // ___p($imlOrder);
        update_post_meta( sanitize_text_field($_POST['order_id']), 'iml-ship-order-params', sanitize_iml_object($imlOrder));
        add_settings_error('iml-error',esc_attr( 'settings_updated' ),
        'Заказ успешно создан в системе IML',
        'updated'
      );
    } catch (CreateOrderError $e) {
      $message = $e->getMessage();
      ___p($message);
      add_settings_error('iml-error',esc_attr( 'settings_updated' ),
      $message,
      'error'
    );
  }
  $this->renderOrderRequestPage($_POST['order_id']);
}else {
  if(isset($_GET['order_id']))
  {
    $this->renderOrderRequestPage($_GET['order_id']);
  }else {
    die();
  }
}


}

private function renderOrderRequestPage($order_id)
{
  $service = Service::getInstance();
  $places = $service->getFullPlacesCollection();
  $conditions = $service->getFullParcelConditionCollection();
  $vatAr = $service->getOption('request_settings', 'VAT');
  $MAX_PLACES_COUNT = $service->getOption('request_settings', 'MAX_PLACES_COUNT');
  $placeList = $this->imlService->getFullPlacesCollection();
  $placeList = json_encode($placeList, JSON_UNESCAPED_UNICODE);
  $deliveryInPVZOnly = $this->cmsFacade->get_option('deliveryInPVZOnly');

  include_once __DIR__.'/Views/Order/request.php';
}

// создание нового заказа после корзины
public function createNewOrderHandler($order_id)
{
  // var_dump($order_id);
  $order = wc_get_order($order_id);
  // var_dump($order);
  $shpMethods = $order->get_shipping_methods();
  $IsIml = false;
  foreach ($shpMethods as $method) {
    if(strpos($method, 'iml') !== false)
    {
      $IsIml = true;
      break;
    }
  }

  if(!$IsIml)
  {
    return;
  }

  if(!isset($_SESSION['iml-ship-order-params']))
  {
    return;
  }

  $imlOrder = $_SESSION['iml-ship-order-params'];

  $imlOrder->Contact = sanitize_text_field($order->get_billing_first_name(). ' ' .$order->get_billing_last_name());
  $imlOrder->Email = sanitize_text_field($order->get_billing_email());
  $imlOrder->Phone = sanitize_text_field($order->get_billing_phone());
  if($imlOrder->isCourierDelivery === true)
  {
    $imlOrder->Address = sanitize_text_field(!empty($order->get_billing_address_1()) ? $order->get_billing_address_1() : $order->get_billing_address_2());
  }else {
    $imlOrder->Address = sanitize_text_field($imlOrder->addressPVZ);
  }
  update_post_meta( $order_id, 'iml-ship-order-params', sanitize_iml_object($imlOrder));
}


public function onChangePaymentMethod()
{
  if(is_checkout())
  {
    include_once __DIR__.'/Views/Order/change_payment.php';
  }
}


public function validateOrder($data, $errors)
{
  $shipping_method_id = $this->imlService->getChosenShippingID();
  if(is_checkout()
  &&   $this->imlService->isImlShippingMethod($shipping_method_id)
  && !$this->imlService->hasCourierService($shipping_method_id)
  && (!isset($_SESSION['iml-pvz-RequestCode']) || empty($_SESSION['iml-pvz-RequestCode'])))
  {
    $errors->add( 'validation', __( 'Не выбран ПВЗ для доставки. Откройте карту по ссылке "выбрать другой"' ));
  }
}

public function getStaticResource($hook)
{

  if($hook == 'settings_page_create-order-iml')
  {
        wp_enqueue_script( 'iml-order-request',
            plugins_url( 'Views/scripts/request.js', __FILE__ ),
            array( 'jquery' ),
            false,
            true
        );
  }
}

public function onAfterGetRates($package_rates, $package)
{
  if (!is_checkout()) {
    return $package_rates;
  }

  unset($_SESSION['iml-ship-order-params']);

  foreach ($package_rates as $rate) {
    $shipping_method_id = $rate->get_method_id();
    
    if (!$this->imlService->isImlShippingMethod($shipping_method_id)) {
      continue;
    }

    if ($shipping_method_id != $this->imlService->getChosenShippingID()) {
      continue;
    }

    $meta     = $rate->get_meta_data();
    $imlOrder = $meta['imlOrder'];

    if (!$imlOrder) {
      continue;
    }

    $imlOrder->addressPVZ    = sanitize_text_field((!$imlOrder->isCourierDelivery && isset($_SESSION['iml-selected-pvz']))    ? $_SESSION['iml-selected-pvz'] : '');
    $imlOrder->DeliveryPoint = sanitize_text_field((!$imlOrder->isCourierDelivery && isset($_SESSION['iml-pvz-RequestCode'])) ? $_SESSION['iml-pvz-RequestCode'] : $imlOrder->DeliveryPoint);

    $_SESSION['iml-ship-order-params'] = $imlOrder;
  }

  return $package_rates;
}


}
