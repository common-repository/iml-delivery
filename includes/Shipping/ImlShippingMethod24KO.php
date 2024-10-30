<?php

namespace Iml\Shipping;

class ImlShippingMethod24KO extends WcImlShippingParent
{

	public function __construct( $instance_id = 0 ) {
// курьерская доставка с наложенным платежом
		$this->id                    = 'iml_method_24ko';
		$this->instance_id           = absint( $instance_id );
		$this->enabled           = 		get_option('enable_method_24ko') ? 'yes' : 'no';
		$this->method_title       = __( get_option('method_24ko_Name') );
		$this->method_description    = __( get_option('method_24ko_Name') );
		$this->title          = get_option('method_24ko_Name');
		$this->Job = '24КО';
		$this->delivery  = 1;
		$this->priceWhenFailCon = get_option('fail_con_method_24ko_price');
		$this->isCourierDelivery = true;
		$this->isCashService = true;
		parent::__construct( $instance_id );
	}



}
