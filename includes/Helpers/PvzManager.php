<?php
namespace Iml\Helpers;

class PvzManager
{

  private $pvzList;
  public function __construct($pvzList)
  {
    $this->pvzList = $pvzList;
  }




  public function getPvzByID($id)
  {
    $cacheKey = 'PvzManager_getPvzByID_'.md5(serialize(func_get_args()));
    $ret = wp_cache_get($cacheKey, IML_CACHE_GROUP);

    if ($ret !== false) {
      return $ret;
    }

    foreach ($this->pvzList as $pvzItem) {
      if($pvzItem['ID'] == $id) {
        $ret = $pvzItem;
        break;
      }
    }

    wp_cache_set($cacheKey, $ret, IML_CACHE_GROUP, IML_CACHE_LIFETIME);

    return $ret;
  }



  public function getPvzListByKey($key, $withPayment)
  {
    // $withPayment - возмоность кассового обслуживания

    $cacheKey = 'PvzManager_getPvzListByKey_'.md5(serialize(func_get_args()));
    $ret = wp_cache_get($cacheKey, IML_CACHE_GROUP);

    if ($ret !== false) {
      return $ret;
    }

    $found = false;
    $preList = [];
    $resultList = [];
    foreach ($this->pvzList as $pvzItem) {
      if($pvzItem['key'] == $key)
      {
        $preList[] = $pvzItem;
        $found = true;
      }else {
        // if($found === true)
        // {
        //   // последовательность найденных ПВЗ прервана
        //   break;
        // }
      }
    }
    if($withPayment) {
        foreach ($preList as $pvzItem ) {
          if($pvzItem['PaymentPossible'] == 1){
            $resultList[] = $pvzItem;
          }
        }

        $ret = $resultList;

    }else {
      $ret = $preList;
    }

    wp_cache_set($cacheKey, $ret, IML_CACHE_GROUP, IML_CACHE_LIFETIME);

    return $ret;
  }


}
