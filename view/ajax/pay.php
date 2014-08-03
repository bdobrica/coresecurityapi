<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

global
	$wp_crm_buyer,
	$wp_crm_state;

$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State ();
$wp_crm_state->set ('state', WP_CRM_State::AddObject);


list ($class, $id) = $_GET['object'] ? explode ('-', $_GET['object']) : explode ('-', $_POST['object']);
if (!class_exists ($class)) die ('Err.');
if (!is_numeric($id)) die ('Err.');

$invoice = new WP_CRM_Invoice ((int) $id);

$object = new WP_CRM_Payment ();
$object->set ('iid', $id);

$structure = new WP_CRM_Form_Structure ($object);
$form = new WP_CRM_Form ($structure);
$form->set ('state', $wp_crm_state->get());

$_GET['object'] = 'WP_CRM_Payment-0';

if ($_POST['object']) $form->action ();


$list = new WP_CRM_List ('WP_CRM_Payment', array ('iid='.$id));
$view = new WP_CRM_View ($list, array (
	'delete' => 'Anuleaza'
	));
$amount = $list->get ('amount');
$value = $invoice->get ('value');

if (!$list->is('empty')) {
	echo '<h3>Istoric plăti:</h3>';
	$view->get (FALSE, TRUE);
	}
else
	$view->get (FALSE, FALSE);

if (abs ($amount - $value) > WP_CRM_Invoice::Epsilon) {
	if (!$list->is('empty'))
		echo '<h3>Au fost plătiti ' . sprintf('%.2f', $amount) . ' lei din ' . sprintf('%.2f', $value). ' lei. Rest de plată ' . sprintf('%.2f', $value - $amount) . '</h3>';
	else
		echo '<h3>Rest de plată ' . sprintf('%.2f', $value - $amount) . ' lei</h3>';
	$form->render (TRUE);
	}
else
	echo '<h3>Factura in valoare de ' . sprintf('%.2f', $value). ' lei a fost achitata integral.</h3>';
?>
