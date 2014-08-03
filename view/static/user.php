<?php
/*
App Title: Companii
App Description:
App Size: 1
App Style:
App Icon: gears 
*/

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


$offices = new WP_CRM_List ('WP_CRM_Office', array (is_numeric ($wp_crm_offices) ? sprintf ('id=%d', $wp_crm_offices) : (!empty($wp_crm_offices) ? sprintf ('id in (%s)', implode (',', $wp_crm_offices)) : 1)));

if ($offices->is ('empty')) {
	$office = new WP_CRM_Office (array ('name' => $current_user->user_email));
	$office->save ();

	$list = new WP_CRM_List ('WP_CRM_Company', array ('oid=' . $office->get ()));
	$view = new WP_CRM_View ($list, array (
		'add' => 'Organizatie Noua',
		'view' => 'Vezi',
		'memo' => 'Memo',
		'delete' => 'Sterge'
		));
	unset ($view);
	}
else
	foreach ($offices->get () as $office) {
		$list = new WP_CRM_List ('WP_CRM_Company', array ('oid=' . $office->get ()));
		$view = new WP_CRM_View ($list, array (
			'add' => 'Organizatie Noua',
			'edit' => 'Modifica',
			'view' => 'Vezi',
			'memo' => 'Memo',
			'delete' => 'Sterge'
			));
		unset ($view);
		}
?>
