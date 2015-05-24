<?php
/*
App Title: ePayment
App Requires: wp_crm_system
App Description:
App Size: 1
App Style:
App Order: 4
App Icon: credit-card
*/

?>
<div class="row">
	<div class="col-md-6">
<?php
	$form = new WP_CRM_Form (array (
		array (
			'class' => 'paypal',
			'fields' => array (
				'paypal_on' => array (
					'type' => 'select',
					'label' => 'Activeaza PayPal',
					'options' => array (
						1 => 'PayPal Activ',
						0 => 'PayPal Inactiv'
						)
					),
				'paypal_key' => array (
					'label' => 'PayPal API Key',
					'default' => 'madalin.matica@gmail.com'
					),
				)
			),
		array (
			'class' => 'buttons',
			'fields' => array (
				'next' => array (
					'type' => 'submit',
					'label' => 'Salveza'
					),
				)
			),
		));
	$form->render (TRUE);
?>
	</div>
	<div class="col-md-6">
<?php
	$form = new WP_CRM_Form (array (
		array (
			'class' => 'paypal',
			'fields' => array (
				'paypal_on' => array (
					'type' => 'select',
					'label' => 'Activeaza MobilPay',
					'options' => array (
						1 => 'MobilPay Activ',
						0 => 'MobilPay Inactiv'
						)
					),
				'paypal_key' => array (
					'label' => 'MobilPay API Key'
					),
				'paypal_ckey' => array (
					'label' => 'MobilPay Private Key'
					),
				'paypal_pkey' => array (
					'label' => 'MobilPay Public Key'
					),
				)
			),
		array (
			'class' => 'buttons',
			'fields' => array (
				'next' => array (
					'type' => 'submit',
					'label' => 'Salveza'
					),
				)
			),
		));
	$form->render (TRUE);
?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h2>Nu s-au inregistrat inca tranzactii cu cardul.</h2>
	</div>
</div>
