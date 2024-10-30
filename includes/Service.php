<?php
namespace Iml;

use Iml\Loaders\{FileDataSaver,LocationsLoader,RegionCityLoader,PickpointPlacesLoader,PlacesListBuilder, CitySorter, StatusesLoader};
use Iml\Helpers\{PlaceManager, PvzManager, StatusManager, AdminOptionsRecorder, CMSFacade};

class Service
{
  private static $instance = null;

  public static function getInstance()
  {
    if(is_null(self::$instance))
    {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getCollectionsPath()
  {
    return __DIR__ .'/../Data';
  }

  public function getLogPath()
  {
    return __DIR__ .'/../Logs';
  }


  public function getPlaceManager()
  {
    $placesList = $this->getFullPlacesCollection();
    return new PlaceManager($placesList);
  }

  public function getPvzManager()
  {

    $filePath = $this->getCollectionsPath() . "/readyPickpoints.json";
    if(file_exists($filePath))
    {
      $pvzs = file_get_contents($filePath);
      $pvzList =json_decode($pvzs, true);
      return new PvzManager($pvzList);
    }

    return false;
  }

  public function getStatusManager()
  {
    $filePath = $this->getCollectionsPath() . "/Statuses.json";
    if(file_exists($filePath))
    {
      $statuses = file_get_contents($filePath);
      $statusList =json_decode($statuses, true);
      return new StatusManager($statusList);
    }

    return false;
  }


  public function getFullPlacesCollection()
  {
    $filePath = $this->getCollectionsPath() . "/allPlaces.json";
    if(file_exists($filePath))
    {
      $places = file_get_contents($filePath);
      return json_decode($places, true);
    }
    return false;
  }



  public function getFullStatusesCollection()
  {
    $filePath = $this->getCollectionsPath() . "/Statuses.json";
    if(file_exists($filePath))
    {
      $places = file_get_contents($filePath);
      return json_decode($places, true);
    }
    return false;
  }

  public function getFullParcelConditionCollection()
  {
    $filePath = $this->getCollectionsPath() . "/Conditions.json";
    if(file_exists($filePath))
    {
      $places = file_get_contents($filePath);
      return json_decode($places, true);
    }
    return false;
  }


  public function getFileSaver()
  {
    return  new FileDataSaver($this->getCollectionsPath());
  }

  public function prepareCollections()
  {
    $fileDataSaver = $this->getFileSaver();
    $this->loadCollections($fileDataSaver);
    $placesListBuilder = new PlacesListBuilder($fileDataSaver, $this->getCollectionsPath(), new CitySorter());
    $placesListBuilder->buildFullCollection();
    update_option('lastUpdateLists' , (new \DateTime())-> format('d.m.Y H:i'));
  }



  public function loadCollections($fileDataSaver)
  {
    $pickPointChecker = new \Iml\Loaders\PickPointChecker($this->getOption('request_settings', 'federal_cities'));
    $cmsFacade = new CMSFacade();
    $deliveryInPVZOnly = $cmsFacade->get_option('deliveryInPVZOnly');
    // print($deliveryInPVZOnly ? 'yes' : 'no');
    // die();

    // $locationsLoader = new LocationsLoader($fileDataSaver);
    // $locationsLoader->loadData();

    $regionCityLoader = new RegionCityLoader($fileDataSaver);
    $regionCityLoader->loadData();

    $pickpointPlacesLoader = new PickpointPlacesLoader($fileDataSaver, $pickPointChecker, $deliveryInPVZOnly);
    $pickpointPlacesLoader->loadData();


    $adminOptionsRecorder = new AdminOptionsRecorder($cmsFacade);
    $statusesLoader = new StatusesLoader($fileDataSaver, $adminOptionsRecorder, $cmsFacade);
    $statusesLoader->loadData($adminOptionsRecorder);
  }


  public function getOption($group, $key)
  {
    $options = include(__DIR__.'/options.php');
    if(isset($options[$group][$key]))
    {
      return $options[$group][$key];
    }

    return false;
  }

  public function getChosenShippingID()
  {
    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
    $chosen_shipping = $chosen_methods[0];
    preg_match ('/(.*):\d+/', $chosen_shipping, $matches);
    if($matches && isset($matches[1]))
    {
      $chosen_shipping = $matches[1];
      return $chosen_shipping;
    }

    return false;
  }


  public function hasCourierService($shippingMethodID)
  {
    return in_array($shippingMethodID, ['iml_method_24', 'iml_method_24ko']);
  }


  public function hasCashService($shippingMethodID)
  {
    return in_array($shippingMethodID, ['iml_method_24ko', 'iml_method_c24ko']);
  }


  public function isImlShippingMethod($shippingMethodID)
  {
    return in_array($shippingMethodID, [
      'iml_method_24',
      'iml_method_24ko',
      'iml_method_c24ko',
      'iml_method_с24'
    ]);
  }

}
