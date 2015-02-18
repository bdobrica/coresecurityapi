<?php
/*
App Title: Profil
App Description:
App Size: 1
App Style:
App Order: 1
App Icon: gears 
*/

?>
<div class="row">
	<div class="col-sm-6">
<?php
try {
	$wp_crm_person = new WP_CRM_Person ($current_user->user_email);
	}
catch (WP_CRM_Exception $wp_crm_exception) {
	$wp_crm_person = new WP_CRM_Person (array (
		'email' => $current_user->user_email
		));
	$wp_crm_person->save ();
	}

$structure = new WP_CRM_Form_Structure ($wp_crm_person);
$form = new WP_CRM_Form ($structure);
$form->set ('state', $wp_crm_state->get());

if ($_POST['object']) $form->action ();
$form->render (TRUE);
?>
	</div>
	<div class="col-sm-6">
<?php
$wp_crm_office_filter = sizeof ($wp_crm_offices) == 1 ? sprintf ('id=%d', current($wp_crm_offices)) : sprintf ('id in (%s)', implode (',', $wp_crm_offices));
$offices = new WP_CRM_List ('WP_CRM_Office', array ($wp_crm_office_filter ? : 'uid=' . $current_user->ID));

if ($offices->is ('empty')) {
	$office = new WP_CRM_Office (array ('name' => $current_user->user_email));
	$office->save ();

	$list = new WP_CRM_List ('WP_CRM_Company', array (sprintf ('oid=%d', $office->get ())));
	$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'add' => array (
					'label' => 'Organizatie Noua'
					)
				)
			),
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
				'view' => array (
					'label' => 'Vezi'
					),
				'memo' => array (
					'label' => 'Memo'
					),
				'delete' => array (
					'label' => 'Sterge'
					)
				))
		));
	unset ($view);
	}
else
	foreach ($offices->get () as $office) {
		$list = new WP_CRM_List ('WP_CRM_Company', array (sprintf ('oid=%d', $office->get ())));
		$view = new WP_CRM_View ($list, array (
			array (
				'type' => 'toolbar',
				'items' => array (
					'add' => array (
						'label' => 'Organizatie Noua'
						)
					)
				),
			array (
				'type' => 'column',
				'label' => 'Actiuni',
				'items' => array (
					'view' => array (
						'label' => 'Vezi'
						),
					'edit' => array (
						'label' => 'Modifica'
						),
					'memo' => array (
						'label' => 'Memo'
						),
					'delete' => array (
						'label' => 'Sterge'
						)
					))
			));
		unset ($view);
		}
?>
	</div>
</div>
