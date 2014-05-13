<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

list ($key, $id) = explode ('-', $_POST['data']);


if (!in_array ($key, array ('invoice_payment', 'invoice_buyer', 'invoice_person', 'invoice_company', 'invoice_products', 'invoice_participants', 'invoice_date', 'invoice_paiddate', 'invoice_delete', 'send_email', 'product_participant', 'instance_name', 'instance_price', 'instance_prices', 'instance_vat', 'instance_fullprice', 'instance_date', 'instance_company', 'instance_location', 'instance_trainer', 'instance_responsible', 'instance_structure', 'instance_participant', 'instance_cnfpa', 'product_customers', 'product_clients', 'product_invoices', 'product_paid_invoices', 'product_sessions', 'uin_generator', 'search_person', 'search_company', 'company_delete', 'company_edit', 'person_delete', 'person_edit', 'client_edit'))) die ('ERROR');

if ($key == 'invoice_payment') {
	$invoice = new WP_CRM_Invoice (intval($id));
	wp_crm_invoice_payment ($invoice, TRUE);
	exit (1);
	}

if ($key == 'invoice_buyer') {
	$invoice = new WP_CRM_Invoice (intval($id));
	wp_crm_invoice_buyer ($invoice, TRUE);
	exit (1);
	}

if ($key == 'invoice_person') {
	$person = new WP_CRM_Person ((int) $id);
	wp_crm_invoice_person ($person, TRUE);
	exit (1);
	}

if ($key == 'invoice_company') {
	$company = new WP_CRM_Company ((int) $id);
	wp_crm_invoice_company ($company, TRUE);
	exit (1);
	}

if ($key == 'invoice_products') {
	$invoice = new WP_CRM_Invoice (intval($id));
	wp_crm_invoice_products ($invoice, TRUE);
	exit (1);
	}

if ($key == 'invoice_participants') {
	$invoice = new WP_CRM_Invoice (intval($id));
	wp_crm_invoice_participants ($invoice, TRUE);
	exit (1);
	}

if ($key == 'product_participant') {
	$participant = new WP_CRM_Person (intval($id));
	wp_crm_product_participant ($participant, TRUE);
	exit (1);
	}

if ($key == 'instance_participant') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series($id), 'number' => wp_crm_extract_number($id)));
	wp_crm_product_instance_participant ($product, TRUE);
	exit (1);
	}

if ($key == 'instance_cnfpa') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series($id), 'number' => wp_crm_extract_number($id)));
	wp_crm_product_instance_cnfpa ($product, TRUE);
	exit (1);
	}

if ($key == 'invoice_date') {
	$invoice = new WP_CRM_Invoice (intval($id));
	wp_crm_invoice_date ($invoice, FALSE, TRUE);
	exit (1);
	}

if ($key == 'invoice_paiddate') {
	$invoice = new WP_CRM_Invoice (intval($id));
	wp_crm_invoice_date ($invoice, TRUE, TRUE);
	exit (1);
	}

if ($key == 'send_email') {
	$invoice = new WP_CRM_Invoice (intval($id));
	wp_crm_send_email ($invoice, TRUE);
	exit (1);
	}

if ($key == 'invoice_delete') {
	$invoice = new WP_CRM_Invoice (intval($id));
	wp_crm_invoice_delete ($invoice, TRUE);
	exit (1);
	}

if ($key == 'product_clients' || $key == 'product_customers') { # client is paying customer
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series($id), 'number' => wp_crm_extract_number($id)));
	wp_crm_product_customers ($product, $key == 'product_clients' ? TRUE : FALSE, TRUE);
	exit (1);
	}

if ($key == 'product_invoices' || $key == 'product_paid_invoices') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series($id), 'number' => wp_crm_extract_number($id)));
	wp_crm_product_invoices ($product, $key == 'product_invoices' ? FALSE : TRUE, TRUE);
	exit (1);
	}

if ($key == 'product_sessions') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series($id), 'number' => wp_crm_extract_number($id)));
	wp_crm_product_sessions ($product, TRUE);
	exit (1);
	}

if ($key == 'uin_generator') {
	echo '<div style="background: #fff; color: #c00; border: 1px solid #ccc; padding: 3px; border-radius: 3px;">Foloseste acest generator de CNP in felul urmator:<ul><li>a) daca nu stii CNP-ul unei persoane, pastreaza-i data nasterii ca data de astazi ('.date('y/m/d').') si seteaza ceilalti parametrii dupa nevoie;</li><li>b) daca ai un CNP gresit si vrei sa-l corectezi: completezi toate datele si generezi astfel doar cifra de control corecta.</li></ul></div>';
	echo '<table class="widefat"><tr><td><select id="uin-generator-gender">
	<option value="1">M</option>
	<option value="2">F</option>
</select>
<input type="text" id="uin-generator-year" value="'.date('y').'" style="width: 2em;"/>
<input type="text" id="uin-generator-month" value="'.date('m').'" style="width: 2em;" />
<input type="text" id="uin-generator-day" value="'.date('d').'" style="width: 2em;" />
<select id="uin-generator-county">
<option value="01">AB</option>
<option value="02">AD</option>
<option value="03">AG</option>
<option value="04">BC</option>
<option value="05">BH</option>
<option value="06">BN</option>
<option value="07">BT</option>
<option value="08">BV</option>
<option value="09">BR</option>
<option value="10">BZ</option>
<option value="11">CS</option>
<option value="12">CJ</option>
<option value="13">CT</option>
<option value="14">CV</option>
<option value="15">DB</option>
<option value="16">DJ</option>
<option value="17">GL</option>
<option value="18">GJ</option>
<option value="19">HR</option>
<option value="20">HD</option>
<option value="21">IL</option>
<option value="22">IS</option>
<option value="2
<option value="36">TL</option>
<option value="37">VS</option>
<option value="38">VL</option>
<option value="39">VR</option>
<option value="40">B</option>
<option value="41">S1</option>
<option value="42">S2</option>
<option value="43">S3</option>
<option value="44">S4</option>
<option value="45">S5</option>
<option value="46">S6</option>
<option value="51">CL</option>
<option value="52">GR</option>
</select>
<input type="text" value="000" id="uin-generator-number" style="width: 3em;" /></td><td>
<input type="button" id="uin-generate" value="Genereaza!" /></td></tr><tr><td colspan="2">
<input type="text" id="uin-generator-uin" class="widefat" /></td></tr></table>';
	}

if ($key == 'search_person' || $key == 'search_company') {
	$type = $key == 'search_person' ? 'person' : 'company';
	echo '<form action="" method="post"><table class="widefat"><tr><th>'.($key == 'search_person' ? 'b) Persoana' : 'Companie').':</th><td><input type="text" name="wp_crm_search_'.$type.'" value="" class="widefat" /></td></tr><tr><td colspan="2" align="center"><input type="button" name="wp_crm_search_'.$type.'" class="wp-crm-ajax-form wp-crm-ajax-append wp-crm-button-primary" value="Cauta" /></td></tr></table></form>';
	exit (1);
	}

if ($key == 'instance_name') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	$name = $product->get('name', $id);
	$nice = $product->get('short name', $id);
	echo '<form action="" method="post"><input type="hidden" name="wp_crm_product_instance" value="'.$id.'" /><input type="hidden" name="wp_crm_product_instance_name_change" value="1" /><table class="widefat">
<tr>
	<th>Nume Facturare: </th><td>'.$name.'</td>
</tr>
<tr>
	<th>Nume Certificat: </th><td>'.$nice.'</td>
</tr>
<tr>
	<th>Tip produs: </th><td><select name="wp_crm_product_instance_type" class="widefat">'.wp_crm_product_types(wp_crm_product_types($name, 'extract')).'</select></td>
</tr>
<tr>
	<th>Denumire: </th><td><input type="text" name="wp_crm_product_instance_name" value="'.$nice.'" class="widefat" /></td>
</tr>
<tr>
	<th>Perioada: </th><td><input type="text" name="wp_crm_product_instance_begin" class="widefat" style="width: 80px;" value="'.date('d-m-Y', $product->get('current begin')).'" /> - <input type="text" name="wp_crm_product_instance_end" class="widefat" style="width: 80px;" value="'.date('d-m-Y', $product->get('current end')).'" /></td>
</tr>
<tr>
	<th>Orasul: </th><td><select name="wp_crm_product_instance_city" class="widefat">'.wp_crm_product_cities(wp_crm_product_cities($name, 'extract')).'</select></td>
</tr>
<tr>
	<th>Optiuni: </th><td><input type="text" name="wp_crm_product_instance_package" value="" class="widefat" /></td>
</tr>
<tr>
	<td colspan="2" align="center"><input type="button" name="wp_crm_product_instance_name_change" value="Modifica!" class="wp-crm-ajax-form wp-crm-button-primary" /></td>
</tr>
</table></form>';
	exit (1);
	}

if ($key == 'instance_price') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	//wp_crm_product_instance_price ($product, null, TRUE);

	echo '<form action="" method="post">
        	<input type="hidden" name="wp_crm_product_instance" value="'.$product->get('current code').'" />
		<input type="hidden" name="wp_crm_product_instance_price_change" value="1" />
	        <table class="widefat">
        	        <tr>
                	        <td>Pret (fara TVA)</td><td><input type="text" name="wp_crm_product_instance_price" value="'.$product->get('price', $id).'" class="widefat" /></td>
	                </tr>
        	        <tr>
                	        <td align="center" colspan="2"><input type="button" name="wp_crm_quick_instance_price_change" value="Adauga!" class="wp-crm-ajax-form" /></td>
	                </tr>
        	</table>
</form>';

	exit (1);
	}

if ($key == 'instance_prices') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	$prices = $product->get('price list');
	$rows = array ();
	foreach ($prices as $price) {
		$rows[] = array (
			'<img src="' . WP_CRM_URL . '/icons/delete.png" alt="" title="" />',
			date('d-m-Y H:i', $price['stamp']),
			round((float)($price['price'] * (100+$price['vat']) * 0.01), 2).' lei',
			round((float)($price['full'] * (100+$price['vat']) * 0.01), 2).' lei',
			$price['vat'].'%',
			);
		}
	echo wp_crm_display_table (array (
		'',
		'Date',
		'Price',
		'Full',
		'VAT',
		), $rows, array ('class' => 'widefat nofooter'));
	exit (1);
	}

/*
if ($key == 'instance_price') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	 echo '<form action="" method="post"><input type="hidden" name="wp_crm_product_instance" value="'.$id.'" /><input type="hidden" name="wp_crm_product_instance_price_change" value="1" /><table class="widefat">
<tr>
        <th>Pret: </th>
        <td><input type="text" name="wp_crm_product_instance_price" value="'.$product->get('price', $id).'" class="widefat"/>
</tr>
<tr>
        <td colspan="2" align="center"><input type="button" name="wp_crm_product_instance_price_change" value="Modifica!" class="wp-crm-ajax-form wp-crm-button-primary" /></td>
</tr>
</table></form>';
        exit (1);
	}
*/

if ($key == 'instance_vat') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	 echo '<form action="" method="post"><input type="hidden" name="wp_crm_product_instance" value="'.$id.'" /><input type="hidden" name="wp_crm_product_instance_vat_change" value="1" /><table class="widefat">
<tr>
        <th>TVA: </th>
        <td><input type="text" name="wp_crm_product_instance_vat" value="'.$product->get('vat', $id).'" class="widefat"/>
</tr>
<tr>
        <td colspan="2" align="center"><input type="button" name="wp_crm_product_instance_vat_change" value="Modifica!" class="wp-crm-ajax-form wp-crm-button-primary" /></td>
</tr>
</table></form>';
        exit (1);
	}

if ($key == 'instance_fullprice') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	 echo '<form action="" method="post"><input type="hidden" name="wp_crm_product_instance" value="'.$id.'" /><input type="hidden" name="wp_crm_product_instance_fullprice_change" value="1" /><table class="widefat">
<tr>
        <th>Full Price: </th>
        <td><input type="text" name="wp_crm_product_instance_fullprice" value="'.$product->get('full price', $id).'" class="widefat"/>
</tr>
<tr>
        <td colspan="2" align="center"><input type="button" name="wp_crm_product_instance_fullprice_change" value="Modifica!" class="wp-crm-ajax-form wp-crm-button-primary" /></td>
</tr>
</table></form>';
        exit (1);
	}

if ($key == 'instance_date') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	echo '<form action="" method="post"><input type="hidden" name="wp_crm_product_instance" value="'.$id.'" /><input type="hidden" name="wp_crm_product_instance_date_change" value="1" /><table class="widefat">
<tr>
	<th>Data: </th>
	<td><input type="text" name="wp_crm_product_instance_date" value="'.date('d-m-Y H:i', $product->get('current stamp')).'" class="widefat"/>
</tr>
<tr>
	<td colspan="2" align="center"><input type="button" name="wp_crm_product_instance_date_change" value="Modifica!" class="wp-crm-ajax-form wp-crm-button-primary" /></td>
</tr>
</table></form>';
	exit (1);
	}

if ($key == 'instance_company') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	echo '<form action="" method="post"><input type="hidden" name="wp_crm_product_instance" value="'.$id.'" /><input type="hidden" name="wp_crm_product_instance_company_change" value="1" /><table class="widefat">
<tr>
	<th>Companie: </th>
	<td><select name="wp_crm_product_instance_company" class="widefat">
		<option value="">Alege o companie:</option>';
	$companies = new WP_CRM_List ('companies', array('flags>0'));
	echo $companies->get ('select', array ('value' => '', 'text' => 'name', 'selected' => $product->get('company')));
	echo '</select>
</tr>
<tr>
	<td colspan="2" align="center"><input type="button" name="wp_crm_product_instance_company_change" value="Modifica!" class="wp-crm-ajax-form wp-crm-button-primary" /></td>
</tr>
</table></form>';
	exit (1);
	}

if ($key == 'instance_location') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	echo '<form action="" method="post"><input type="hidden" name="wp_crm_product_instance" value="'.$id.'" /><input type="hidden" name="wp_crm_product_instance_location_change" value="1" /><table class="widefat">
<tr>
	<th>Locatie: </th>
	<td><select name="wp_crm_product_instance_location" class="widefat">
		<option value="">Alege o locatie:</option>';
	$locations = new WP_CRM_List ('locations');
	echo $locations->get ('select', array ('value' => '', 'text' => 'name', 'selected' => $product->get('location')));
	echo '</select>
</tr>
<tr>
	<td colspan="2" align="center"><input type="button" name="wp_crm_product_instance_location_change" value="Modifica!" class="wp-crm-ajax-form wp-crm-button-primary" /></td>
</tr>
</table></form>';
	exit (1);
	}

if ($key == 'instance_trainer') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	echo '<form action="" method="post"><input type="hidden" name="wp_crm_product_instance" value="'.$id.'" /><input type="hidden" name="wp_crm_product_instance_trainer_change" value="1" /><table class="widefat">
<tr>
	<th>Trainer: </th>
	<td><select name="wp_crm_product_instance_trainer" class="widefat">
		<option value="">Alege un trainer:</option>';
	$trainers = new WP_CRM_List ('trainers');
	echo $trainers->get ('select', array ('value' => '', 'text' => 'name', 'selected' => $product->get('trainer')));
	echo '</select>
</tr>
<tr>
	<td colspan="2" align="center"><input type="button" name="wp_crm_product_instance_trainer_change" value="Modifica!" class="wp-crm-ajax-form wp-crm-button-primary" /></td>
</tr>
</table></form>';
	exit (1);
	}

if ($key == 'instance_responsible') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	echo '<form action="" method="post"><input type="hidden" name="wp_crm_product_instance" value="'.$id.'" /><input type="hidden" name="wp_crm_product_instance_responsible_change" value="1" /><table class="widefat">
<tr>
	<th>Trainer: </th>
	<td><select name="wp_crm_product_instance_responsible" class="widefat">
		<option value="">Alege un responsabil:</option>';
	$responsibles = new WP_CRM_List ('responsibles');
	echo $responsibles->get ('select', array ('value' => '', 'text' => 'name', 'selected' => $product->get('responsible')));
	echo '</select>
</tr>
<tr>
	<td colspan="2" align="center"><input type="button" name="wp_crm_product_instance_trainer_change" value="Modifica!" class="wp-crm-ajax-form wp-crm-button-primary" /></td>
</tr>
</table></form>';
	exit (1);
	}

if ($key == 'instance_structure') {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($id), 'number' => wp_crm_extract_number ($id)));
	wp_crm_structure ($product, TRUE);
	exit (1);
	}

if ($key == 'person_delete') {
	$person = new WP_CRM_Person (intval($id));
	wp_crm_person_delete ($person, TRUE);
	exit (1);
	}

if ($key == 'person_edit') {
	$person = new WP_CRM_Person (intval($id));
	wp_crm_invoice_person ($person, TRUE);
	exit (1);
	}

if ($key == 'company_delete') {
	$company = new WP_CRM_Company (intval($id));
	wp_crm_company_delete ($company, TRUE);
	exit (1);
	}

if ($key == 'company_edit') {
	$company = new WP_CRM_Company (intval($id));
	wp_crm_invoice_company ($company, TRUE);
	exit (1);
	}

if ($key == 'client_edit') {
	$client = new WP_CRM_Client (array ('voucher' => $id));
	wp_crm_client_edit ($client, TRUE);
	exit (1);
	}

print_r($_POST);
?>
