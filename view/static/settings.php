<?php
/*
App Title: Settings
App Parent: ecommerce
App Requires: wp_crm_admin
App Order: 1
App Description:
App Size: 1
App Style:
App Icon: cogs
*/

$wp_crm_settings = new WP_CRM_Settings ();

$structure = new WP_CRM_Form_Structure ($wp_crm_settings);

$form = new WP_CRM_Form ($structure);
$form->set ('state', $wp_crm_state->get());

if (!empty ($_POST)) $form->action ();
$form->render (TRUE);
?>
