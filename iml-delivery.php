<?php

/**
 * @wordpress-plugin
 * Plugin Name:       IML for Woocommerce
 * Plugin URI:        
 * Description:       Модуль для расчета стоимости доставки через API Iml
 * Version:           1.0.3
 * Author URI:        https://ipol.ru
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}


define('IML_DELIVERY_PLUGIN_VERSION', '1.0.3');

if (!defined('IML_DELIVERY_PLUGIN_URI')) {
	define('IML_DELIVERY_PLUGIN_URI', plugin_dir_url(__FILE__));
}

if (!defined('IML_DELIVERY_PLUGIN_PATH')) {
	define('IML_DELIVERY_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
}

if (!isset($iml_delivery_spl_autoloader) || $iml_delivery_spl_autoloader === false) {
    include_once "bootstrap.php";
}


//$developHosts = ['localhost', 'wordpress-test.iml.ru'];
//
//if(in_array($_SERVER['SERVER_NAME'], $developHosts))  {
//	define('IS_DEVELOP_VERSION', true);
//}
//
//
//
//
//
//
//if (defined( 'IS_DEVELOP_VERSION'))
//{
//	error_reporting(E_ALL); // включаем сообщения об ошибках
//	ini_set('display_errors', 1); // включаем показ ошибок на экран
//	define( 'WP_SENTRY_DSN', 'http://fceb3b469b9a4eddb78d9af2027d8479:1eb3b45b462147a6b794029a39ce27b7@sentry-test.iml.ru/18' );
//	define( 'WP_SENTRY_ERROR_TYPES', E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_USER_DEPRECATED );
//	define( 'WP_SENTRY_PUBLIC_DSN', 'http://fceb3b469b9a4eddb78d9af2027d8479:1eb3b45b462147a6b794029a39ce27b7@sentry-test.iml.ru/18' );
//	// define( 'WP_SENTRY_VERSION', 'v9.0.0' );
//	define( 'WP_SENTRY_ENV', 'development' );
//}


register_activation_hook( __FILE__, function () {
	$instance = Iml\Service::getInstance();
	$instance->prepareCollections();
});


register_deactivation_hook( __FILE__, function () {
	$options = include(__DIR__.'/includes/options.php');
	foreach ($options['plugin_settings'] as $option) {
		unregister_setting( $option['group'], $option['name']);
		delete_option($option['name']);
	}

	$timestamp = wp_next_scheduled( 'iml_cron_hook' );
	wp_unschedule_event( $timestamp, 'iml_cron_hook' );
});


function ___p($var)
{
	if (defined( 'IS_DEVELOP_VERSION'))
	{
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
	}else {
		$logged = Iml\Helpers\Logger::getInstance();
		$logged->debug('', $var);
	}
}


function wp_iml_getView($viewPath)
{
	$path = dirname( __FILE__ ) . "/includes/Views/{$viewPath}.php";
	if(!file_exists($path))
	{
		throw new \Exception("Не найдено представление {$path}", 1);
	}
	return $path;
}


function run_iml_delivery() {

	$plugin = new Iml\ImlDelivery();
	$plugin->run();

}

function sanitize_iml_object($imlOrder)
{
	foreach(get_object_vars($imlOrder) as $k => $v) {
		$imlOrder->$k = sanitize_text_field($v);
	}

	return $imlOrder;
}

run_iml_delivery();
