<?php
class WP_CRM_Payment extends WP_CRM_Model {
	const Cash	=  1;
	const Card	=  2;
	const Bank	=  4;
	const Receipt	=  8;
	const Treasury	= 16;
	const OnLine	= 32;
	const Mobile	= 64;

	public static $T = 'payments';
	public static $K = array (
		'uid',
		'iid',
		'cid',
		'type',
		'series',
		'number',
		'amount',
		'details',
		'stamp'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`iid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`type` int(11) NOT NULL DEFAULT 0',
		'`series` varchar(3) NOT NULL DEFAULT \'\'',
		'`number` int(11) NOT NULL DEFAULT 0',
		'`amount` float(9,2) NOT NULL DEFAULT 0.00',
		'`details` text NOT NULL',
		'`stamp` int(11) NOT NULL'
		);

	public static $F = array (
		'view' => array (
			'type:array;types' => 'Tip',
			'amount' => 'Valoare',
			'details' => 'Detalii',
			'stamp:date' => 'Data',
			'iid:hidden' => 'Invoice'
			),
		'new' => array (
			'type:array;types' => 'Tip',
			'amount' => 'Valoare',
			'details' => 'Detalii',
			'stamp:date' => 'Data',
			'iid:hidden' => 'Invoice'
			),
		'edit' => array (
			'type:array;types' => 'Tip',
			'amount' => 'Valoare',
			'details' => 'Detalii',
			'stamp:date' => 'Data',
			'iid:hidden' => 'Invoice'
			),
		);

	private static $types = array (
		self::Cash	=> 'Bon Fiscal (casa de marcat)',
		self::Card	=> 'Card Bancar (POS)',
		self::Bank	=> 'Transfer Bancar (OP)',
		self::Receipt	=> 'Chitanta (numerar)',
		self::Treasury	=> 'Trezorerie (OP)',
		self::OnLine	=> 'Plata Electronica',
		self::Mobile	=> 'Telefon Mobil (on-line)'
		);

	public static function mobilpay ($invoice, $echo = FALSE) {
		include (dirname(__FILE__) . '/Mobilpay/Payment/Request/Abstract.php');
		include (dirname(__FILE__) . '/Mobilpay/Payment/Request/Card.php');
		include (dirname(__FILE__) . '/Mobilpay/Payment/Invoice.php');
		include (dirname(__FILE__) . '/Mobilpay/Payment/Address.php');


		$paymentUrl = TRUE ?
				'https://sandboxsecure.mobilpay.ro' :
				'https://secure.mobilpay.ro';

		if (!file_exists (dirname(__FILE__).'/Mobilpay/Security/acreditate.cer'))
			throw new WP_CRM_Exception ('Missing Security Certificate', WP_CRM_Exception::Missing_Security);

		$x509FilePath 	= dirname(__FILE__).'/Mobilpay/Security/acreditate.cer';
		try {
			srand((double) microtime() * 1000000);
			$objPmReqCard 					= new Mobilpay_Payment_Request_Card();
			$objPmReqCard->signature 			= 'QG7T-YM3D-6BM3-U614-S1P2';
			$objPmReqCard->orderId 				= $invoice->get('series');
			$objPmReqCard->confirmUrl 			= get_bloginfo('stylesheet_directory') . '/ajax/mobilpay.php?inv=' . $invoice->get('id');
			$objPmReqCard->returnUrl 			= get_bloginfo('url') . '/mobilpay?inv=' . $invoice->get('id');

			$objPmReqCard->invoice = new Mobilpay_Payment_Invoice();
			$objPmReqCard->invoice->currency	= 'RON';
			$objPmReqCard->invoice->amount		= $invoice->get('value');
			$objPmReqCard->invoice->installments	= '1';
			$objPmReqCard->invoice->details		= $invoice->get('series');

			$billingAddress 			= new Mobilpay_Payment_Address();
			$billingAddress->type			= $invoice->get('buyer');
			$billingAddress->firstName		= $invoice->buyer->get('first_name');
			$billingAddress->lastName		= $invoice->buyer->get('last_name');
			$billingAddress->fiscalNumber		= $invoice->buyer->get('uin');
			$billingAddress->identityNumber		= $invoice->get('buyer') == 'person' ? '' : $invoice->buyer->get('rc');
			$billingAddress->country		= 'ROMANIA';
			$billingAddress->county			= $invoice->buyer->get('county');
			$billingAddress->city			= '';
			$billingAddress->zipCode		= '';
			$billingAddress->address		= $invoice->buyer->get('address');
			$billingAddress->email			= $invoice->buyer->get('email');
			$billingAddress->mobilePhone		= $invoice->buyer->get('phone');
			$billingAddress->bank			= '';
			$billingAddress->iban			= '';

			$objPmReqCard->invoice->setBillingAddress($billingAddress);

			$objPmReqCard->invoice->setShippingAddress($billingAddress);

			$objPmReqCard->encrypt($x509FilePath);
			}
		catch(Exception $e) {
			}

		if (!isset($e) || !($e instanceof Exception)) {
			$out .= '<p>Ai posibilitatea sa platesti <strong>chiar acum</strong>, online, factura primita pe email, cu ajutorul cardului. Apasa butonul <strong>PLATESTE ACUM</strong> si urmeaza pasii, completand cu atentie toate campurile indicate. Plata online se realizeaza securizat prin intermediul <a href="https://www.mobilpay.ro" target="_blank" title="MobilPay">MobilPay</a>. Pentru a plati online, trebuie sa fii de acord cu <a href="/tos" target="_blank">termenii si conditiile ' . $invoice->seller->get('name') . '</a> pentru plata online!</p>
	<p>Alte modalitati de plata, conform instructiunilor primite deja prin email, sunt urmatoarele:
	<ul type="square">
		<li>prin ordin de plata sau virament bancar</li>
		<li>prin numerar sau card, la sediul companiei noastre</li>
	</ul>
	<p>Iti multumim pentru increderea acordata ' . $invoice->seller->get('name') . '!</p>';
			$out .= '<form method="post" action="'.$paymentUrl.'" target="_blank" style="text-align: center;"><input type="hidden" name="env_key" value="' . $objPmReqCard->getEnvKey() . '" /><input type="hidden" name="data" value="' . $objPmReqCard->getEncData() . '" /><button class="btn btn-sm btn-success">PLATESTE ACUM!</button></form><div style="text-align: center;"><img src="' . get_bloginfo ('stylesheet_directory') . '/images/mobilpay.png" /></div>';
			}
		else {
			$out .= '<p>Pentru moment, sistemul de plata online nu este functional.</p>';
			}

		if ($echo) echo $out;
		return $out;
		}

	public static function paypal ($invoice, $echo = FALSE) {
		$exchange = new WP_CRM_Currency ();
		$amount = $exchange->convert ($invoice->get ('value'), 'RON', 'EUR');
		$out .= '<p>Ai posibilitatea sa platesti <strong>chiar acum</strong>, online, factura primita pe email, cu ajutorul cardului. Apasa butonul <strong>PayPal Check Out</strong> si urmeaza pasii, completand cu atentie toate campurile indicate. Plata online se realizeaza securizat prin intermediul <a href="https://www.paypal.com" target="_blank" title="PayPal">PayPal</a>. Pentru a plati online, trebuie sa fii de acord cu <a href="/tos" target="_blank">termenii si conditiile ' . $invoice->seller->get('name') . '</a> pentru plata online!</p>
	<p>Alte modalitati de plata, conform instructiunilor primite deja prin email, sunt urmatoarele:
	<ul type="square">
		<li>prin ordin de plata sau virament bancar</li>
		<li>prin numerar sau card, la sediul companiei noastre</li>
	</ul>
	<p>Iti multumim pentru increderea acordata ' . $invoice->seller->get('name') . '!</p>';
		$out .= '
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="text-align: center;">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="madalin.matica@gmail.com" />
<input type="hidden" name="item_name" value="' . $invoice->get ('product_list') . '" />
<input type="hidden" name="invoice" value="' . $invoice->get ('series') . '" />
<input type="hidden" name="amount" value="' . sprintf ('%.2f', $amount) . '" />
<input type="hidden" name="currency_code" value="EUR" />

<input type="hidden" name="return" value="https://api.acreditate.ro/paypal-success" />
<input type="hidden" name="cancel_return" value="https://api.acreditate.ro/paypal-cancel" />
<input type="hidden" name="notify_url" value="https://api.acreditate.ro/wp-content/themes/wp-crm/ajax/widget/paypal.php" />
<input type="image" name="submit" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_xpressCheckout.gif" alt="PayPal - The safer, easier way to pay online">
</form>';
		if ($echo) echo $out;
		return $out;
		}
	
	public static function num2wrd ($number) {
		$words = array (
			1 => array ('unu', 'doi', 'trei', 'patru', 'cinci', 'sase', 'sapte', 'opt', 'noua'),
			10 => array ('zece', 'douazeci', 'treizeci', 'patruzeci', 'cincizeci', 'saizeci', 'saptezeci', 'optzeci', 'nouazeci'),
			100 => array ('o suta', 'doua sute', 'trei sute', 'patru sute', 'cinci sute', 'sase sute', 'sapte sute', 'opt sute', 'noua sute'),
			1000 => array ('o mie', 'doua mii', 'trei mii', 'patru mii', 'cinci mii', 'sase mii', 'sapte mii', 'opt mii', 'noua mii')
			);

		$integer = intval($number);
		$decimal = intval(100 * ($number - $integer));

		$out = '';

		$value = $integer%100;

		if ($value) {
			if ($value < 10) $out = $words[1][$value - 1] . ' ' . $out;
			else
			if ($value == 10) $out = $words[10][0] . $out;
			else
			if ($value < 20) $out = $words[1][$value%10 - 1] . 'sprezece ' . $out;
			else {
				if ($value % 10)
					$out = $words[10][intval($value/10) - 1] . ' si ' . $words[1][$value%10 - 1] . ' ' . $out;
				else
					$out = $words[10][intval($value/10) - 1] . ' ' . $out;
				}
			}

		if ($integer) $out .= ($value > 0 || $value < 20) ? 'lei' : 'de lei';

		$integer = intval($integer/100);
		$value = $integer%10;

		if ($value) $out = $words[100][$value - 1] . ' ' . $out;
		$integer = intval ($integer/10);
		$value = $integer%10;

		if ($value) $out = $words[1000][$value - 1] . ' ' . $out;

		if ($decimal) {
			if ($decimal < 10) $out .= ' si ' . $words[1][$decimal - 1];
			else
			if ($decimal == 10) $out .= ' si ' . $words[10][0];
			else
			if ($decimal < 20) $out .= ' si ' . $words[1][$decimal%10 - 1] . 'sprezece';
			else {
				if ($decimal % 10)
					$out .= ' si ' . $words[10][intval($decimal/10) - 1] . ' si ' . $words[1][$decimal%10 - 1];
				else
					$out .= ' si ' . $words[10][intval($decimal/10) - 1];
				}
			$out .= ($decimal < 20) ? ' bani' : ' de bani';
			}

		return str_replace ('unusprezece', 'unsprezece', $out);
		}

	public function is ($key = null) {
		switch ((string) $key) {
			case 'cash':
				return (((int) $this->data['type']) & self::Cash) == self::Cash ? TRUE : FALSE;
				break;
			case 'card':
				return (((int) $this->data['type']) & self::Card) == self::Card ? TRUE : FALSE;
				break;
			case 'bank':
				return (((int) $this->data['type']) & self::Bank) == self::Bank ? TRUE : FALSE;
				break;
			case 'receipt':
				return (((int) $this->data['type']) & self::Receipt) == self::Receipt ? TRUE : FALSE;
				break;
			default:
				return FALSE;
			}
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'types':
				/*
				TODO: filter this->types acccording to each company
				*/
				return self::$types;
				break;
			case 'series':
				return $this->data['series'] . str_pad ($this->data['number'], 5, 0, STR_PAD_LEFT);
				break;
			default:
				return parent::get ($key, $opts);
			}
		}

	public function save () {
		global $wpdb;

		parent::save ();

		if ($this->data['amount'] > 0) {
			$invoice = new WP_CRM_Invoice ((int) $this->data['iid']);
			$invoice->pay ((int) $this->data['stamp']);

			if (((int) $this->data['type']) == self::Receipt) {
				$this->data['series'] = 'R' . $invoice->seller->get ('invoice_series');
				
				$sql = $wpdb->prepare ('select 1+max(coalesce(number,0)) from `' . $wpdb->prefix . static::$T . '` where series=%s', $this->data['series']);
				$this->data['number'] = (int) $wpdb->get_var ($sql);

				$sql = $wpdb->prepare ('update `'. $wpdb->prefix . static::$T .'` set series=%s,number=%d where id=%d;', array (
					$this->data['series'],
					$this->data['number'],
					$this->ID
					));
				$wpdb->query ($sql);
				}
			}
		}
	}
?>
