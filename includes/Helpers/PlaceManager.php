<?php

namespace Iml\Helpers;

class PlaceManager
{

  const SIM_MIN_COEF = 90;
  private $placesList;
  public function __construct($placesList)
  {
    $this->placesList = $placesList;
  }

  public function find($city, $region, $job)
  {
    $cacheKey = 'PlaceManager_find_'. md5(serialize(func_get_args()));

    $ret = wp_cache_get($cacheKey, IML_CACHE_GROUP);

    if ($ret !== false) {
      return $ret;
    }

    $ret = false;

    $city = trim(mb_strtoupper(str_ireplace('ё', 'е', $city)));
    $city = str_ireplace(['Г.', 'город'], '', $city);
    
    if($region) {
      $region = trim(mb_strtoupper(str_ireplace('ё', 'е', $region)));
      $region = str_ireplace ( ['РЕСП.', 'КРАЙ.', 'ОБЛ.'], ['РЕСПУБЛИКА', 'КРАЙ', 'ОБЛАСТЬ'], $region);
      $region = str_ireplace(['Г.', 'город'], '', $region);
    }

    foreach ($this->placesList as $key => $item) {
      if ($region) {
        similar_text($city, $item['city'], $percCity);
        similar_text($region, $item['region'], $percRegion);

        if($percCity > self::SIM_MIN_COEF &&
          $percRegion > self::SIM_MIN_COEF &&
          in_array($job, $item['Jobs'])
        ){
          $ret = array_merge($item, compact('key'));
          break;
        }
      }  else {
        similar_text($city, $item['city'], $percCity);
        if ($percCity > self::SIM_MIN_COEF && in_array($job, $item['Jobs'])) {
          $ret = array_merge($item, compact('key'));
          break;
        }
      }
    }

    wp_cache_set($cacheKey, $ret, IML_CACHE_GROUP, IML_CACHE_LIFETIME);

    return $ret;
  }


  public function getPlacesByJob($Job)
  {
    $cacheKey = 'placeManager_getPlacesByJob'. md5(serialize(func_get_args()));
    $ret = wp_cache_get($cacheKey, IML_CACHE_GROUP);

    if ($ret !== false) {
      return $ret;
    }

    $resultList = [];
    foreach ($this->placesList as $key => $item) {
        if(in_array($Job, $item['Jobs']))
        {
          $resultList[] = array_merge($item, compact('key'));
        }
    }

    wp_cache_set($cacheKey, $placesList, IML_CACHE_GROUP, IML_CACHE_LIFETIME);

    return $resultList;
  }



  public function getPlaceByKey($key)
  {
    return isset($this->placesList[$key]) ? $this->placesList[$key] : false;
  }



}
