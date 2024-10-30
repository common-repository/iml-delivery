<?php

namespace Iml;

use Iml\{PluginSettings, PvzSelector, ImlAjaxHandler, OrderHandler};
use Iml\Helpers\{CartInfo2OrderConverter, CMSFacade, StatusConverter, Logger};
use Iml\Service;


class ImlDelivery
{


  protected $loader;
  protected $plugin_name;
  protected $version;
  private $orderHandler;
  private $service;
  private $imlAjaxHandler;
  protected $environment = 'production';



  public function __construct($environment = 'production', $version = '1.0.0')
  {
	    $this->plugin_name = 'iml-delivery';
	    $this->version = $version;
	    $this->environment = $environment;
	    $cmsFacade = new CMSFacade();
	    $this->service = Service::getInstance();
	    $logger = new Logger($this->service->getLogPath());
	    $placeManager = $this->service->getPlaceManager();
	    $pvzManager = $this->service->getPvzManager();
	    $statusManager = $this->service->getStatusManager();
	    $cartInfo2OrderConverter = new CartInfo2OrderConverter($cmsFacade, $this->service);

	    $orderStatusConverter  = new StatusConverter($cmsFacade);
	    $this->orderHandler = new OrderHandler($cartInfo2OrderConverter,
	    new \Iml\CreateOrder\Factory($cmsFacade),
	    new \Iml\Statuses\Factory($cmsFacade),
	    $statusManager,
	    $this->service,
	    $orderStatusConverter,
	    $cmsFacade
	  );


	  $this->imlAjaxHandler = new ImlAjaxHandler(
	    $this->service,
	    $placeManager,
	    $pvzManager,
	    $this->orderHandler,
	    new \Iml\PrintBar\Factory($cmsFacade, $this->service->getFileSaver()),
	    $orderStatusConverter,
	    new \Iml\CalcDelivery\Api\Factory($cmsFacade),
	    $cartInfo2OrderConverter
	  );

	  $this->load_dependencies();
  }


public function addStatusOrderRefreshInterval( $schedules )
{
  $schedules['one_hour'] = array(
    'interval' => 3600,
    'display'  => esc_html__( 'Every hour' ),
  );
  $schedules['iml_one_day'] = array(
    'interval' => 86400,
    'display'  => esc_html__( 'At every day' ),
  );
  return $schedules;
}


private function load_dependencies()
{
  $this->loader = new ImlDeliveryLoader();
  $pluginSettings = new PluginSettings();


  $this->loader->add_filter( 'cron_schedules', $this, 'addStatusOrderRefreshInterval');
  $this->loader->add_action( 'iml_cron_hook', $this->orderHandler, 'scheduleImlRequestStatuses' );
  $this->loader->add_action( 'iml_cron_hook2', $this->service, 'prepareCollections' );

  $this->loader->add_action( 'init', $this, 'init');
  $this->loader->add_action( 'wp_login', $this, 'endSession');
  $this->loader->add_action( 'wp_logout', $this, 'endSession');
  $this->loader->add_action( 'admin_init', $pluginSettings, 'registerSettings');
  $this->loader->add_action( 'admin_menu', $pluginSettings , 'adminMenu' );
  $this->loader->add_action( 'wp_ajax_loadImlRegions', $this->imlAjaxHandler, 'loadImlRegions' );
  $this->loader->add_action( 'wp_ajax_getPvzByPlaceKey', $this->imlAjaxHandler, 'getPvzByPlaceKey' );
  $this->loader->add_action( 'wp_ajax_updateImlRequestStatus', $this->imlAjaxHandler, 'updateImlRequestStatus' );
  $this->loader->add_action( 'wp_ajax_printBar4Order', $this->imlAjaxHandler, 'printBar4Order' );
  $this->loader->add_action( 'wp_ajax_getPlacesByJob', $this->imlAjaxHandler, 'getPlacesByJob' );
  $this->loader->add_action( 'wp_ajax_setSelectedPvz', $this->imlAjaxHandler, 'setSelectedPvz' );
  $this->loader->add_action( 'wp_ajax_getPlaceKeyByPvzID', $this->imlAjaxHandler, 'getPlaceKeyByPvzID' );
  $this->loader->add_action( 'wp_ajax_nopriv_setSelectedPvz', $this->imlAjaxHandler, 'setSelectedPvz' );
  $this->loader->add_action( 'wp_ajax_nopriv_setSelectedPvz', $this->imlAjaxHandler, 'setSelectedPvz' );
  $this->loader->add_action( 'wp_ajax_recalcDeliveryCost', $this->imlAjaxHandler, 'recalcDeliveryCost' );
  $this->loader->add_action( 'woocommerce_checkout_update_order_meta', $this->orderHandler, 'savePvz2OrderField');
  $this->loader->add_action( 'woocommerce_thankyou', $this->orderHandler, 'display4User');
  $this->loader->add_action( 'woocommerce_view_order',$this->orderHandler,  'display4User');
  $this->loader->add_action( 'woocommerce_admin_order_data_after_order_details',$this->orderHandler,  'display4Admin');
  $this->loader->add_action( 'woocommerce_process_shop_order_meta', 'saveExtraDetails', 45, 2 );
  $this->loader->add_action( 'manage_shop_order_posts_custom_column', $this->orderHandler, 'adminOrderValues', 10, 2 );
  $this->loader->add_action( 'woocommerce_after_checkout_validation', $this->orderHandler, 'validateOrder', 10, 2);
  $this->loader->add_action('admin_menu', $this->orderHandler,  'createPageIMLRequest');
  $this->loader->add_action( 'woocommerce_thankyou', $this->orderHandler, 'createNewOrderHandler' );
  $this->loader->add_action( 'admin_enqueue_scripts', $this->orderHandler, 'getStaticResource' );
  $this->loader->add_action( 'admin_enqueue_scripts', $this, 'wpse_enqueue_datepicker' );
  $this->loader->add_action( 'woocommerce_saved_order_items', $this->orderHandler, 'savedOrderItems', 10);
  $this->loader->add_action('update_option_deliveryInPVZOnly', $this->imlAjaxHandler, 'deliveryPvzTypeChanged', 10, 3);

  // register custom shipping methods
  $this->loader->add_filter('woocommerce_shipping_methods', $this, 'registerShippingMethods', 10);
  $this->loader->add_filter( 'woocommerce_after_shipping_calculator', $this->imlAjaxHandler, 'showPvzSelector' );
  $this->loader->add_action( 'woocommerce_review_order_before_payment', $this->imlAjaxHandler, 'showPvzSelector' );
  $this->loader->add_filter( 'wp_footer', $this->orderHandler, 'onChangePaymentMethod' );
  $this->loader->add_filter( 'manage_edit-shop_order_columns', $this->orderHandler, 'adminOrderColumns');
  $this->loader->add_filter( 'woocommerce_cart_calculate_shipping_address', $this->orderHandler, 'clearPVZCode');


}




public function wpse_enqueue_datepicker()
{
	wp_register_style( 'jquery-ui',  IML_DELIVERY_PLUGIN_URI .'/assets/jquery-ui.js');

	wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_style( 'jquery-ui' );
}




public function init()
{
  if(!session_id()) {
    session_start();
  }

  wp_clear_scheduled_hook( 'iml_cron_hook' );
  wp_clear_scheduled_hook( 'iml_cron_hook2' );
  if ( ! wp_next_scheduled( 'iml_cron_hook' ) ) {
    wp_schedule_event( time(), 'one_hour', 'iml_cron_hook' );
  }

  if ( ! wp_next_scheduled( 'iml_cron_hook2' ) ) {
    wp_schedule_event( time(), 'iml_one_day', 'iml_cron_hook2' );
  }
}

public function endSession() {
  session_destroy ();
}


public function registerShippingMethods($methods)
{
  // курьерка с предоплатой
  if(get_option('enable_method_24'))
  {
    $methods[ 'iml_method_24' ] = 'Iml\Shipping\ImlShippingMethod24';
  }
  // курьерка с наложенным платежом
  if(get_option('enable_method_24ko'))
  {
    $methods[ 'iml_method_24ko' ] = 'Iml\Shipping\ImlShippingMethod24KO';
  }
  // доставка до ПВЗ с наложенным платежом
  if(get_option('enable_method_c24ko'))
  {
    $methods[ 'iml_method_c24ko' ] = 'Iml\Shipping\ImlShippingMethodC24KO';
  }
  // доставка до ПВЗ с предоплатой
  if(get_option('enable_method_c24'))
  {
    $methods[ 'iml_method_с24' ] = 'Iml\Shipping\ImlShippingMethodC24';
  }
  return $methods;
}


public function run()
{
  return $this->loader->run();
}

public function get_plugin_name() {
  return $this->plugin_name;
}

public function get_loader() {
  return $this->loader;
}


public function get_version() {
  return $this->version;
}
}
