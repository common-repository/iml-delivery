<?php

namespace Iml\Shipping;

class ImlShippingMethodC24KO extends WcImlShippingParent
{

	public function __construct( $instance_id = 0 ) {
//  доставка до ПВЗ с наложенным платежом
		$this->id                    = 'iml_method_c24ko';
		$this->instance_id           = absint( $instance_id );
		$this->enabled           = 		get_option('enable_method_c24ko') ? 'yes' : 'no';
		$this->method_title       = __( get_option('method_c24ko_Name') );
		$this->method_description    = __( get_option('method_c24ko_Name') );
		$this->title          = get_option('method_c24ko_Name');
		$this->Job = 'С24КО';
		$this->delivery  = 2;
		$this->priceWhenFailCon = get_option('fail_con_method_c24ko_price');
		$this->isCourierDelivery = false;
		$this->isCashService = true;
		parent::__construct( $instance_id );
	}



}
