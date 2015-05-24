<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

global
	$wp_crm_buyer,
	$wp_crm_state,
	$wp_crm_helper;

$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State ();
$wp_crm_state->set ('state', WP_CRM_State::AddObject);

echo "<!-- MODAL_TITLE: Cerinte Produs -->";

list ($class, $ids) = $_GET['object'] ? explode ('-', $_GET['object']) : explode ('-', $_POST['object']);
if (!class_exists ($class)) die ('Err.');

$ids = explode (',', $ids);
if (empty($ids)) die ('Err.');

$list = new WP_CRM_List ('WP_CRM_Requirement', array ('rid in (' . implode (',', $ids) .')', 'class=\'' . $class . '\''));
$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'context' => htmlentities (json_encode (array ('parent' => $class . '-' . implode (',', $ids)))),
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
				'view' => array (
					'label' => 'Detalii',
					),
				)
			)
	));
unset ($view);
/*
$objects = array ();

foreach ($ids as $id) $objects[] = new $class ($id);

$wp_crm_helper = new WP_CRM_Requirement ($objects);

$structure = new WP_CRM_Form_Structure ($wp_crm_helper);
$form = new WP_CRM_Form ($structure);
$form->set ('state', $wp_crm_state->get());

if ($_POST['object']) $form->action ();
$form->render (TRUE);
*/
?>