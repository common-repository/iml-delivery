<?php

namespace Iml\Shipping;

class ImlShippingMethodC24 extends WcImlShippingParent
{

	public function __construct( $instance_id = 0 ) {
//  доставка до ПВЗ с предоплатой
		$this->id                    = 'iml_method_с24';
		$this->instance_id           = absint( $instance_id );
		$this->enabled           = 		get_option('enable_method_c24') ? 'yes' : 'no';
		$this->method_title       = __( get_option('method_c24_Name') );
		$this->method_description    = __( get_option('method_c24_Name') );
		$this->title          = get_option('method_c24_Name');
		$this->Job = 'С24';
		$this->delivery  = 2;
		$this->priceWhenFailCon = get_option('fail_con_method_c24_price');
		$this->isCourierDelivery = false;
		$this->isCashService = false;
		parent::__construct( $instance_id );
	}



}
