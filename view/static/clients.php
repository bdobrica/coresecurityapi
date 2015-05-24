<?php
/*
App Title: Clients
App Parent: ecommerce
App Requires: wp_crm_admin
App Order: 3
App Description:
App Size: 1
App Style:
App Icon: users
*/
?>
<div class="row">
	<div class="col-sm-12">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-persons">Persoane Fizice</a></li>
			<li><a href="#tab-companies">Persoane Juridice</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab-persons">
				<div class="row">
					<div class="col-lg-12">
<?php
$list = new WP_CRM_List ('WP_CRM_Client');
$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'add' => array (
					'label' => 'Adauga',
					),
				)
			),
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
				'add' => array (
					'label' => 'Adauga',
					),
				'edit' => array (
					'label' => 'Modifica',
					),
				'invoice' => array (
					'label' => 'Factura',
					),
				)
			)
		));
unset ($view);
?>
					</div>
				</div>
			</div>
			<div class="tab-pane active" id="tab-companies">
				<div class="row">
					<div class="col-lg-12">
<?php
$list = new WP_CRM_List ('WP_CRM_Company', array ('id<>1'));
$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'add' => array (
					'label' => 'Adauga',
					),
				)
			),
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
				'add' => array (
					'label' => 'Adauga',
					),
				'edit' => array (
					'label' => 'Modifica',
					),
				'invoice' => array (
					'label' => 'Factura',
					),
				)
			)
		));
unset ($view);
?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
