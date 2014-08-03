<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$uin = $_GET['u'];
$delegate = NULL;

if (preg_match('/^[0-9]{13}$/', $uin))
	$buyer = $wpdb->get_row ($wpdb->prepare('select * from `'.$wpdb->prefix.'persons` where uin=%s;', $uin));
else {
	$uin = trim(preg_replace('/[^0-9A-Z]+/', '', strtoupper($uin)));
	$buyer = $wpdb->get_row ($wpdb->prepare('select * from `'.$wpdb->prefix.'companies` where uin=%s;', $uin));
	if ($buyer)
		$delegate_id = $wpdb->get_var ($wpdb->prepare('select pid from `'.$wpdb->prefix.'employees` where cid=%d order by flags desc limit 0,1;', $buyer->id));
	if ($delegate_id)
		$delegate = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'persons` where id=%d;', $delegate_id));
	}

if (!$buyer) die (json_encode(array()));

$out = array ();

if (strlen($buyer->uin) == 13) {
	$out = array (
		'name' => $buyer->name,
		'address' => $buyer->address,
		'phone' => $buyer->phone,
		'email' => $buyer->email,
		'delegate' => $buyer->name,
		'delegateuin' => $buyer->uin,
		'delegateemail' => $buyer->email,
		'delegatephone' => $buyer->phone,
		);
	}
else {
	$out = array (
		'name' => $buyer->name,
		'reg' => $buyer->rc,
		'address' => $buyer->address,
		'account' => $buyer->account,
		'county' => $buyer->county,
		'bank' => $buyer->bank,
		);
	if ($delegate) {
		$out['delegate'] = $delegate->name;
		$out['delegateuin'] = $delegate->uin;
		$out['delegateemail'] = $delegate->email;
		$out['delegatephone'] = $delegate->phone;
		}
	}

echo json_encode($out);
?>
