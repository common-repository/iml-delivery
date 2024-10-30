<?php

namespace Iml\Loaders;

class LocationsLoader extends PlacesLoader
{
  public function loadData()
  {

//    $data = file_get_contents ('http://list.iml.ru/Location?type=json');
	  $data = wp_remote_retrieve_body(wp_remote_get('http://list.iml.ru/Location?type=json'));
      $newData = [];
      foreach (json_decode($data, true) as $location) {

          $upperRegionCode = mb_strtoupper($location['RegionCode'], 'UTF-8');

          if (!empty($location['OpeningDate']) && strtotime($location['OpeningDate']) >= time()) {
              continue;
          }

          if ($location['RegionCode'] == 'ПОЧТА') {
              continue;
          }

          //if ($location ['Submission'] != '')
          //continue;

          if ($location['ReceiptOrder'] <= 0) {
              continue;
          }

          if(!in_array($upperRegionCode, $newData))
          {
            $newData[] = $upperRegionCode;
          }

      }
      $newData = json_encode($newData, JSON_UNESCAPED_UNICODE);
      $this->dataSaver->save('Location.json', $newData);
  }

}
