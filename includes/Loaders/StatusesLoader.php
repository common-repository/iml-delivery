<?php

namespace Iml\Loaders;

class StatusesLoader extends PlacesLoader
{

  protected $dataSaver;
  private $adminOptionsRecorder;
  private $cmsFacade;

  public function __construct($dataSaver, $adminOptionsRecorder, $cmsFacade)
  {
    $this->dataSaver = $dataSaver;
    $this->adminOptionsRecorder = $adminOptionsRecorder;
    $this->cmsFacade = $cmsFacade;
  }


  public function loadData()
  {

//    $data = file_get_contents ('http://list.iml.ru/status?type=json');
	  $data = wp_remote_retrieve_body(wp_remote_get('http://list.iml.ru/status?type=json'));
      $orderStatuses = [];
      $orderConditions =  [];
      foreach (json_decode($data, true) as $item) {

        if($item['StatusType'] == 1)
        {
          $orderStatuses[$item['Code']] = $item;
        }else if($item['StatusType'] == 40 && !empty($item['Name'])){
          $optionName = $this->adminOptionsRecorder->handle($item);
          $orderConditions[$optionName] = $item;
        }
      }

      $this->cmsFacade->add_option('iml_order_conditions', array_keys($orderConditions));
      $orderStatuses = json_encode($orderStatuses, JSON_UNESCAPED_UNICODE);
      $orderConditions = json_encode($orderConditions, JSON_UNESCAPED_UNICODE);
      $this->dataSaver->save('Conditions.json', $orderConditions);
      $this->dataSaver->save('Statuses.json', $orderStatuses);
  }

}
