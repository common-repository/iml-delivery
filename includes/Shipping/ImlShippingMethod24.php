<?php

namespace Iml\Shipping;

class ImlShippingMethod24 extends WcImlShippingParent
{

	public function __construct( $instance_id = 0 ) {
// курьерская доставка с предоплатой
		$this->id                    = 'iml_method_24';
		$this->instance_id           = absint( $instance_id );
		$this->enabled           = 		get_option('enable_method_24') ? 'yes' : 'no';
		$this->method_title       = __( get_option('method_24_Name') );
		$this->method_description    = __( get_option('method_24_Name') );
		$this->title          = get_option('method_24_Name');
		$this->Job = '24';
		$this->delivery  = 1;
		$this->priceWhenFailCon = get_option('fail_con_method_24_price');
		$this->isCourierDelivery = true;
		$this->isCashService = false;
		parent::__construct( $instance_id );
	}




}
