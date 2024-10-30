<?php

namespace Iml\Loaders;

class PlacesListBuilder
{
  private $dataSaver;
  private $collectionsPath;
  private $citySorter;

  private $federalCities = ['МОСКВА Г.', 'САНКТ-ПЕТЕРБУРГ Г.', 'МОСКВА', 'САНКТ-ПЕТЕРБУРГ', 'СЕВАСТОПОЛЬ'];

  public function __construct($dataSaver, $collectionsPath, $citySorter)
  {
    $this->dataSaver = $dataSaver;
    $this->collectionsPath = $collectionsPath;
    $this->citySorter = $citySorter;
  }



  private function getCollKey($city, $area, $region)
  {
    // $result = ($area) ? sprintf("%s_%s_%s",$city, $area, $region ) : sprintf("%s_%s",$city, $region );
    $result = sprintf("%s_%s",$city, $region );
    return preg_replace('/\s/u', '_', $result);
  }


  private function prepareCity($city)
  {
    $city = mb_strtoupper($city, 'UTF-8');
    $city = str_ireplace('Г.', '', $city);
    $city = trim($city);
    return $city;
  }


  private function prepareRegion($region)
  {
    $region = mb_strtoupper($region, 'UTF-8');
    $region = str_ireplace('Г.', '', $region);
    $region = str_ireplace ( ['РЕСП.', 'КРАЙ.', 'ОБЛ.'], ['РЕСПУБЛИКА', 'КРАЙ', 'ОБЛАСТЬ'], $region);
    $region = trim($region);
    return $region;
  }

  private function prepareArea($area)
  {
    $area = mb_strtoupper($area, 'UTF-8');
    $area = str_ireplace('Р-Н.', '  РАЙОН', $area);
    return $area;
  }

  private function getTitle($city, $area, $region)
  {
    return ($area) ? sprintf("%s (%s, %s)",$city, $area, $region) : sprintf("%s (%s)",$city, $region);
  }

  public function buildFullCollection()
  {

    // список, формируемый на основе списка мест пвз и списка мест курьерки
    $resultCollection = [];
    $pPlaces = file_get_contents($this->collectionsPath . '/PPlaces.json');
    $regionCities = file_get_contents($this->collectionsPath . '/RegionCity.json');

    // список всех  пвз для возможности выбора в заявке
    $resultPickpoints = [];


    foreach (json_decode($pPlaces, true) as $item) {

      if (empty($item['FormRegion']) && in_array($item['FormCity'], $this->federalCities)) {
        //особые условия для столицы
        $item['FormRegion'] = $item['FormCity'];
      } elseif (empty($item['FormCity']) && in_array($item['FormRegion'], $this->federalCities)) {
        $item['FormCity'] = $item['FormRegion'];
      }

      $realCityName = $item['realFormCity'];
      $realRegionName = $item['realFormRegion'];
      $city   = $this->prepareCity($item['FormCity']);
      $region = $this->prepareRegion($item['FormRegion']);
      if(!$city || !$region)
      {
        continue;
      }
      $area = $this->prepareArea($item['FormalizedArea']);
      $regionCode = mb_strtoupper($item['RegionCode'], 'UTF-8');

      $RequestCode = $item['RequestCode'];

      $key = $this->getCollKey($city, $area, $region);
      $title = $this->getTitle($city, $area, $region);

      $Job = $item['PaymentPossible'] == 1 ? 'С24КО' : 'С24';

      $resultPickpoints[] = [
        'key' => $key,
        'regionCode' => $regionCode,
        'RequestCode' => $item['RequestCode'],
        'CmptdAddress' => $item['CmptdAddress'],
        'PaymentPossible' => $item['PaymentPossible'],
        "ID" => $item['ID']
      ];

      $Jobs = [$Job];
      // если доступна услуга кассового обслуживания - должна быть простая услуга С24
      if($Job == 'С24КО')
      {
        $Jobs[] = 'С24';
      }
      if(!array_key_exists($key, $resultCollection))
      {

        $resultCollection[$key] = compact('city', 'region', 'area',
        'regionCode', 'title', 'realCityName', 'realRegionName', 'RequestCode',
        'Jobs', 'key'
      );
    }
    else {
          if(!in_array($Job, $resultCollection[$key]['Jobs']))
          {
            $resultCollection[$key]['Jobs'][] = $Job;
          }
          //укажем последний ПВЗ с поддержкой КО в данном населенном пункте - чтобы api не выдавало ошибок
          if($Job == 'С24КО')
          {
            $resultCollection[$key]['RequestCode'] = $RequestCode;
          }
    }

  }

  foreach (json_decode($regionCities, true) as $item) {

    $city   = $this->prepareCity($item['City']);
    $region = $this->prepareRegion($item['Region']);
    if(!$city || !$region)
    {
      continue;
    }
    $area = $this->prepareArea($item['Area']);
    $regionCode = mb_strtoupper($item['RegionIML'], 'UTF-8');

    $key = $this->getCollKey($city, $area, $region);
    $title = $this->getTitle($city, $area, $region);
    $realCityName = '';
    $realRegionName = '';
    $RequestCode = '';


    if(!array_key_exists($key, $resultCollection))
    {
      // потом проверять доступность услуг по ResourceLimit
      $Jobs = ['24КО', '24'];
      $resultCollection[$key] = compact('city', 'region', 'area', 'regionCode', 'title',
      'realCityName', 'realRegionName', 'RequestCode', 'Jobs', 'key');
    }else {
      if(!in_array('24КО', $resultCollection[$key]['Jobs']))
      {
        $resultCollection[$key]['Jobs'][] = '24КО';
      }
      if(!in_array('24', $resultCollection[$key]['Jobs']))
      {
        $resultCollection[$key]['Jobs'][] = '24';
      }
    }
  }

// ___p($resultCollection);
// die();

  $resultCollection = $this->citySorter->sort($resultCollection);
  $resultCollection = json_encode($resultCollection, JSON_UNESCAPED_UNICODE);
  $this->dataSaver->save('allPlaces.json', $resultCollection);

  $resultPickpoints = json_encode($resultPickpoints, JSON_UNESCAPED_UNICODE);
  $this->dataSaver->save('readyPickpoints.json', $resultPickpoints);
}

}
