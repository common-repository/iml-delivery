<?php
namespace Iml;

use Iml\Service;
class PluginSettings
{

  public  function adminMenu() {
    add_options_page(
      'Настройки работы с IML доставкой',
      'IML доставка',
      'manage_options',
      'iml-settings',
      array(
        $this,
        'settingsPage'
      )
    );
  }


  public function getSafeValue($value, $default)
  {
    return empty(get_option($value)) ? $default : get_option($value);
  }

  public function registerSettings() {
    $iml_order_conditions = get_option('iml_order_conditions');
    if($iml_order_conditions && is_array($iml_order_conditions))
    {
      foreach ($iml_order_conditions as  $value) {
        //по-умолчанию - 1 - разрешено
        add_option($value, 1);
        register_setting( 'iml-sg-vars', $value);
      }
    }

    $options = include(__DIR__.'/options.php');
    foreach ($options['plugin_settings'] as $option) {
      $default = (isset($option['default']) ? $option['default'] : '');
      $type = (isset($option['type']) ? $option['type'] : '');
      $sanitize_callback = (isset($option['sanitize_callback']) ? $option['sanitize_callback'] : '');
      add_option($option['name'], $default);
      register_setting( $option['group'], $option['name'], ['type' => $type, 'sanitize_callback' => $sanitize_callback]);
    }
  }


  private function renderLoginPage()
  {
    $service = Service::getInstance();
    $places = $service->getFullPlacesCollection();
    include_once dirname( __FILE__ ) . "/Views/Settings/login.php";
  }



  private function renderVarsPage()
  {
    $service = Service::getInstance();
    $conditions = $service->getFullParcelConditionCollection();
    include_once dirname( __FILE__ ) . "/Views/Settings/vars.php";
  }


  public function  settingsPage() 
  {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $tabsActions = [
      'login' => 'Авторизация',
      'main' => 'Расчет',
      'cart' => 'Методы доставки',
      'vars' => 'Коррекция',
      'status' => 'Статусы заказов',
      'dev' => 'Справочники IML'
    ];
    
    global $imlSetActiveTab;

    $imlSetActiveTab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'login';

    require_once dirname( __FILE__ ) . "/Views/Settings/index.php";
  }

}
