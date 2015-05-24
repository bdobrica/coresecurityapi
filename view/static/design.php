<?php
/*
App Title: Design
App Parent: ecommerce
App Requires: wp_crm_admin
App Order: 6
App Description:
App Size: 1
App Style:
App Icon: picture-o
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
$list = new WP_CRM_List ('WP_CRM_Client', array ('pid='.((int)$_GET['filter'])));
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
				'view' => array (
					'label' => 'Vezi',
					),
				'invoice' => array (
					'label' => 'Factura',
					),
				'pay' => array (
					'label' => 'Plati',
					),
				)
			)
		));
unset ($view);
?>
					</div>
				</div>
			</div>
			<div class="tab-pane active" id="tab-persons">
				<div class="row">
					<div class="col-lg-12">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>