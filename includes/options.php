<?php

return [
'plugin_settings' =>
[
  array('name' => 'iml-login', 'group' => 'iml-sg-login', 'default' => ''),
  array('name' => 'iml-password', 'group' => 'iml-sg-login', 'default' => ''),
  array('name' => 'departureCity', 'group' => 'iml-sg-login'),
  array('name' => 'testMode', 'group' => 'iml-sg-main', 'default' => false,  'type' => 'boolean' ),
  array('name' => 'iml_calc_method', 'group' => 'iml-sg-main', 'default' => 'GetPlantCostOrder'),
  array('name' => 'calcType', 'group' => 'iml-sg-main', 'default' => 'Сервер IML'),
  array('name' => 'cdOwnRegionPrice', 'group' => 'iml-sg-main', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array('name' => 'cdOtherRegionPrice', 'group' => 'iml-sg-main', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array('name' => 'pkOwnRegionPrice', 'group' => 'iml-sg-main', 'type' => 'number', 'sanitize_callback' => 'absint'  ),
  array('name' => 'pkOtherRegionPrice', 'group' => 'iml-sg-main', 'type' => 'number', 'sanitize_callback' => 'absint'  ),
  array('name' => 'defaultWeightKg', 'group' => 'iml-sg-main',  'default' => 1, 'type' => 'number'),
  array('name' => 'defaultLength', 'group' => 'iml-sg-main',  'default' => '10', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array('name' => 'defaultWidth', 'group' => 'iml-sg-main',  'default' => '10', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array('name' => 'defaultHeight', 'group' => 'iml-sg-main',  'default' => '10', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array('name' => 'defaultPlacesCount', 'group' => 'iml-sg-main',  'default' => 1, 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array('name' => 'enableFullfilment', 'group' => 'iml-sg-main', 'default' => false),

  array('name' => 'packageCalcType', 'group' => 'iml-sg-main', 'default' => 'fix-pack'),
array('name' => 'defaultGoodWeightKg', 'group' => 'iml-sg-main',  'default' => 1, 'type' => 'number' ),
  array('name' => 'defaultGoodLength', 'group' => 'iml-sg-main',  'default' => '10', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array('name' => 'defaultGoodWidth', 'group' => 'iml-sg-main',  'default' => '10', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array('name' => 'defaultGoodHeight', 'group' => 'iml-sg-main',  'default' => '10', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array( 'name' => 'addExtraWeightKg', 'group' => 'iml-sg-main', 'type' => 'number' ),


  array('name' => 'method_24ko_Name', 'group' => 'iml-sg-cart',  'default' => 'IML доставка курьером (с наложенным платежом)', 'sanitize_callback' => 'sanitize_text_field' ),
  array('name' => 'method_24_Name', 'group' => 'iml-sg-cart',  'default' => 'IML доставка курьером (с предоплатой)', 'sanitize_callback' => 'sanitize_text_field' ),
  array('name' => 'method_c24ko_Name', 'group' => 'iml-sg-cart',  'default' => 'IML доставка до ПВЗ (с наложенным платежом)', 'sanitize_callback' => 'sanitize_text_field' ),
  array('name' => 'method_c24_Name', 'group' => 'iml-sg-cart',  'default' => 'IML доставка до ПВЗ (с предоплатой)', 'sanitize_callback' => 'sanitize_text_field' ),
  array('name' => 'conIMLtimeout', 'group' => 'iml-sg-cart',  'default' => 20, 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array('name' => 'showIMLDelWhenFailCon', 'group' => 'iml-sg-cart', 'default' => false),
  array('name' => 'fail_con_method_24ko_price', 'group' => 'iml-sg-cart', 'default' => 300, 'type' => 'number', 'sanitize_callback' => 'absint' ), //failConMethod1Price
  array('name' => 'fail_con_method_24_price', 'group' => 'iml-sg-cart', 'default' => 300, 'type' => 'number', 'sanitize_callback' => 'absint'),
  array('name' => 'fail_con_method_c24ko_price', 'group' => 'iml-sg-cart', 'default' => 300, 'type' => 'number', 'sanitize_callback' => 'absint'),
  array('name' => 'fail_con_method_c24_price', 'group' => 'iml-sg-cart', 'default' => 300, 'type' => 'number', 'sanitize_callback' => 'absint'),
  array('name' => 'enable_method_24ko', 'group' => 'iml-sg-cart', 'default' => true ), //enableMethod1
  array('name' => 'enable_method_24', 'group' => 'iml-sg-cart', 'default' => true ),
  array('name' => 'enable_method_c24ko', 'group' => 'iml-sg-cart', 'default' => true ),
  array('name' => 'enable_method_c24', 'group' => 'iml-sg-cart', 'default' => true ),
  array('name' => 'showDateDelivery', 'group' => 'iml-sg-cart', 'default' => true ),
  array('name' => 'deliveryInPVZOnly', 'group' => 'iml-sg-cart', 'default' => false ),

  array( 'name' => 'addDeliveryDateDays', 'group' => 'iml-sg-vars', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array( 'name' => 'dpExtraOper', 'group' => 'iml-sg-vars' ),
  array( 'name' => 'dpExtraNumber', 'group' => 'iml-sg-vars', 'type' => 'number', 'sanitize_callback' => 'absint' ),
  array( 'name' => 'dpExtraMeasure', 'group' => 'iml-sg-vars' ),
  array( 'name' => 'roundingOper', 'group' => 'iml-sg-vars', 'default' => 'no' ),
  array( 'name' => 'roundingMeasure', 'group' => 'iml-sg-vars' ),
  array( 'name' => 'enableValuatedAmount', 'group' => 'iml-sg-vars'),

  array( 'name' => 'StsAccepted', 'group' => 'iml-sg-status'),
  array( 'name' => 'StsHand2Courier', 'group' => 'iml-sg-status'),
  array( 'name' => 'StsOnPickpont', 'group' => 'iml-sg-status'),
  array( 'name' => 'StsDelivered', 'group' => 'iml-sg-status'),
  array( 'name' => 'StsCanceled', 'group' => 'iml-sg-status'),

  array('name' => 'lastUpdateLists', 'group' => 'iml-sg-extra', 'default' =>false),

  
],
'request_settings' =>
[
  'VAT' => [
    6 => 'Без НДС',
    5 => '0%',
    2 => '10%',
    1 => '20%'
  ],
  'defaultVAT' => 1,
  'MAX_PLACES_COUNT' => 9,
  // города федерального значения, ПВЗ в которых по особенному обрабатываются при загрузке с list.iml.ru/sd
  'federal_cities' => ['МОСКВА Г.', 'САНКТ-ПЕТЕРБУРГ Г.', 'МОСКВА', 'САНКТ-ПЕТЕРБУРГ', 'СЕВАСТОПОЛЬ']
]

];
