<?php
/*
App Title: Profil
App Description:
App Size: 1
App Style:
App Icon: user 
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

$wp_crm_offices = new WP_CRM_List ('WP_CRM_Office', array ($wp_crm_office_query ? $wp_crm_office_query : 1));

if ($wp_crm_offices->is ('empty')) {
	$wp_crm_office = new WP_CRM_Office (array ('name' => $current_user->user_email));
	$wp_crm_office->save ();

	$list = new WP_CRM_List ('WP_CRM_Company', array ('oid=' . $wp_crm_office->get ()));
	$view = new WP_CRM_View ($list, array (
		'add' => 'Organizatie Noua',
		'view' => 'Vezi',
		'memo' => 'Memo',
		'delete' => 'Sterge'
		));
	unset ($view);
	}
else
	foreach ($wp_crm_offices->get () as $wp_crm_office) {
		$list = new WP_CRM_List ('WP_CRM_Company', array ('oid=' . $wp_crm_office->get ()));
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
