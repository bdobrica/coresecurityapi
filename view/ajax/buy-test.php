<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
#include (dirname(__FILE__) . '/common.php');

global
	$wp_crm_buyer,
	$wp_crm_state;

$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State ();
$wp_crm_state->set ('state', WP_CRM_State::AddObject);

$event = substr (basename(__FILE__), 0, strrpos (basename (__FILE__), '.'));
$event = 'buy';

$data = $_GET['object'] ? $_GET['object'] : $_POST['object'];

list ($data_o, $filter) = explode (';', $data);
list ($class, $id) = explode ('-', $data_o);

if (!class_exists ($class)) die ('Err.');
if (!is_numeric($id)) die ('Err.');

$object = new $class ((int) $id);

$requirements = new WP_CRM_List ('WP_CRM_Requirement', array (
					sprintf ('event=%s', $event),
					sprintf ('oid=%d', $object->get()),
					sprintf ('class=%s', get_class ($object))
					));

print_r ($requirements);

/*
try {
	$object->buy ();
	echo 'OK';
	}
catch (WP_CRM_Exception $wp_crm_exception) {
	echo 'EROARE: Te-ai inscris deja la acest curs!';
	}
*/

/*
$structure = new WP_CRM_Form_Structure ($object);
if (!empty ($append)) 
	$structure->append ($append);

$form = new WP_CRM_Form ($structure);
$form->set ('state', $wp_crm_state->get());

if ($_POST['object']) {
	$form->action ();
	}

$form->render (TRUE);
*/

/**
 * If the user has companies attached to the account, should allow choosing between them.
 */

/**
 * The product has some requirements. If they are not met, they should be filled before
 * allowing the user to buy it.
 */


?>
