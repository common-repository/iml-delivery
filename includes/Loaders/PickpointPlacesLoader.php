<?php

namespace Iml\Loaders;

class PickpointPlacesLoader extends PlacesLoader
{

  private $pickPointChecker;
  private $deliveryInPVZOnly;


  public function __construct($dataSaver, $pickPointChecker, $deliveryInPVZOnly)
  {
    parent::__construct($dataSaver);
    $this->pickPointChecker = $pickPointChecker;
    $this->deliveryInPVZOnly = $deliveryInPVZOnly;
  }


  private function formatPickpointAddress($pickpoint, $map)
  {

      //формируем новый формат адреса для ПВЗ
      $CmptdAddress  = '';
      $addressParams = [];
      foreach ($map as $field) {
          if (isset($pickpoint[$field]) && $pickpoint[$field]) {
              $addressParams[] = $pickpoint[$field];
          }
      }

      if (count($addressParams) > 0) {
          return implode(', ', $addressParams);
      } else if (isset($pickpoint['Address'])) {
          return $pickpoint['Address'];
      }

      return false;
  }

  public function loadData()
  {
    $url = $this->deliveryInPVZOnly ? 'http://list.iml.ru/sd?type=json&sdtype=1' : 'http://list.iml.ru/sd?type=json';

//    $data = file_get_contents($url);
	$data = wp_remote_retrieve_body(wp_remote_get($url));
    $newData = [];
    foreach (json_decode($data, true) as $ppItem) {

      if (!$this->pickPointChecker->isCorrectPickpoint($ppItem)) {
          continue;
      }

      $upperFormCity   = mb_strtoupper($ppItem['FormCity'], 'UTF-8');
      $upperFormRegion = mb_strtoupper($ppItem['FormRegion'], 'UTF-8');
      $upperFormalizedArea = mb_strtoupper($ppItem['FormalizedArea'], 'UTF-8');
      $upperRegionCode = mb_strtoupper($ppItem['RegionCode'], 'UTF-8');
      $RequestCode = $ppItem['RequestCode'];
      $cmptdAddress = $this->formatPickpointAddress($ppItem, array('FormCity', 'FormStreet', 'FormHouse', 'FormBuilding'));
      $newData[] = [
      'FormCity' => $upperFormCity,
      'FormRegion' => $upperFormRegion,
      'FormalizedArea' => $upperFormalizedArea,
      'RegionCode' =>  $upperRegionCode,
      'RequestCode' => $RequestCode,
      'realFormCity' => $ppItem['FormCity'],
      'realFormRegion' => $ppItem['FormRegion'],
      'CmptdAddress' => $cmptdAddress,
      'PaymentPossible' => $ppItem['PaymentPossible'],
      'ID' => $ppItem['ID']
    ];

    }

    $newData = json_encode($newData, JSON_UNESCAPED_UNICODE);
    $this->dataSaver->save('PPlaces.json', $newData);
}

}
