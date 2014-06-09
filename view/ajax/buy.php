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

$event = substr (basename(__FILE__), 0, strrpos (basename (__FILE__), '.'));

$data = $_GET['object'] ? $_GET['object'] : $_POST['object'];

list ($data_o, $filter) = explode (';', $data);
list ($class, $id) = explode ('-', $data_o);

if (!class_exists ($class)) die ('Err.');
if (!is_numeric($id)) die ('Err.');

$object = new $class ((int) $id);
	
$requirements = new WP_CRM_List ('WP_CRM_Requirement', array (
					sprintf ('event=\'%s\'', $event),
					sprintf ('oid=%d', $object->get()),
					sprintf ('class=\'%s\'', get_class ($object))
					));

$structure = new WP_CRM_Form_Structure ($requirements, $object);
if (!$structure->is ('empty')) {
?>
	<div class="alert alert-danger">
		Pentru a te putea inscrie la acest curs va trebui sa completezi corespunzator campurile de mai jos.
	</div>
<?php
	$form = new WP_CRM_Form ($structure);
	$form->set ('state', $wp_crm_state->get());

	if ($_POST['object']) {
		$form->action ();
		}

	echo 'Campurile de mai jos sunt obligatorii pentru inscrierea la curs!'; 
	$form->render (TRUE);
	}
else {
	try {
		$object->buy ();
		echo 'OK';
		}
	catch (WP_CRM_Exception $wp_crm_exception) {
		echo 'EROARE: Te-ai inscris deja la acest curs!';
		}
	}
?>
