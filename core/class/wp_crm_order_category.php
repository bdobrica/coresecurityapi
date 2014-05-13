<?php
class WP_CRM_Order_Category extends WP_CRM_Model {
	private static $types = array (
		'Marketing'	=> array (
			'Price'			=> 1,
			'Advertising'		=> 2,
			'CustomerService'	=> 3,
			'DirectSales'		=> 4,
			'DistributionMargin'	=> 5,
			'CreditPeriod'		=> 6,
			),
		'Operations'	=> array (
			'RnD'			=> 7,
			'Manufacturing'		=> 8,
			),
		'Finance'			=> 9
		);

	
	}
?>
