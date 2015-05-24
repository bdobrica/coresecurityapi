<?php
/*
App Title: Live
App Parent: elearning
App Requires: wp_crm_add_livestream
App Order: 12
App Description:
App Size: 1
App Style:
App Icon: video-camera
*/
?>

<?php
$role = $wp_crm_user->get ('role');
if (in_array ($role, array ('wp_crm_subscriber'))) {
?>
	<div class="wp-crm-view-live-player"></div>
<?php
	}
else {
?>
<div class="row">
	<div class="col-md-6">
	<script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
		<div id="simpleflash"></div>
	<script>
	swfobject.embedSWF('<?php bloginfo('stylesheet_directory'); ?>/assets/swf/simple.swf', 'simpleflash', 640, 400, '9.0.0', {}, {'r5w': 640, 'r5h': 400, 'r5live': 'live', 'r5uri': 'rtmp://gw.einvest.ro/live', 'r5path': 'test'});
	</script>
	</div>
	<div class="col-md-6">
<?php
$form = new WP_CRM_Form (array (
	array (
		'fields' => array (
			'title' => array (
				'label' => 'Titlul Cursului'
				),
			'description' => array (
				'label' => 'Descrierea Cursului',
				'type' => 'rte'
				),
			)
		),
	array (
		'class' => 'buttons',
		'fields' => array (
			'submit' => array (
				'label' => 'Salveaza',
				'type' => 'submit'
				),
			)
		)
	));
$form->render (TRUE);
?>
	</div>
</div>
<?php
}
?>
