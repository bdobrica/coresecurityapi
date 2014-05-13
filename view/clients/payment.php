<div class="wp-crm-view-payment-wrap">
	<div class="wp-crm-view-payment">
<?php
$err = 0;
if (isset($_GET['inv']) && preg_match ('/^[A-z ]+[0-9 ]+$/', trim($_GET['inv']))) {
	$_GET['inv'] = str_replace (array (' ', "\n", "\r", "\t"), '', $_GET['inv']);
	try {
		$invoice = new WP_CRM_Invoice ($_GET['inv']);
		$err = 1;
		}
	catch (WP_CRM_Exception $wp_crm_exception) {
		$err = 2;
		}
	}

if (isset($_GET['cli']) && preg_match ('/^[0-9A-z ]+$/', trim($_GET['cli']))) {
	$_GET['cli'] = str_replace (array (' ', "\n", "\r", "\t"), '', $_GET['cli']);
	$_GET['cli'] = str_replace ('o', '0', strtolower($_GET['cli']));
	$data = (int) base_convert ($_GET['cli'], WP_CRM_Invoice::ID_Base, 10);
	try {
		$client = $data%2 ?
			new WP_CRM_Company ((int) (floor($data/2) - WP_CRM_Company::Padding)) :
			new WP_CRM_Person  ((int) (floor($data/2) - WP_CRM_Person::Padding));
		$err = $err ? $err : 1;
		}
	catch (WP_CRM_Exception $wp_crm_exception) {
		$err = 2;
		}
	}

switch ($err) {
	case 1: ?>
		<div class="wp-crm-view-mobilpay">
<?php		try {
			WP_CRM_Payment::mobilpay ($invoice);
			}
		catch (WP_CRM_Exception $wp_crm_exception) { ?>
		<p class="wp-crm-view-payment-error">Platile online pentru <?php echo $invoice->seller->get('name'); ?> sunt momentan sistate.</p>
<?php			} ?>
		</div>
<?php		break;
	case 2: ?>
		<p class="wp-crm-view-payment-error">A intervenit o eroare. Va rugam sa verificati seria si numarul facturii (afisate cu rosu in partea superioara a facturii pe care ati primit-o prin email) sau ID-ul de client (imediat sub seria facturii).</p>
<?php	default: ?>
		<form action="" method="get">
			<label>Serie si numar factura: </label>
			<input type="text" name="inv" value="" />
			<label>ID Client: </label>
			<input type="text" name="cli" value="" />
			<p>
				<button>Plateste &raquo;</button>
			</p>
		</form>
<?php	} ?>
	</div>
</div>
