<?php
$curl = curl_init ('http://gw.einvest.ro/tools/scanner.php');

curl_setopt_array ($curl, array (
	CURLOPT_CUSTOMREQUEST	=> 'GET',
	CURLOPT_POST		=> false,
	CURLOPT_USERAGENT	=> 'WP_CRM/1.0 (Linux)',
	CURLOPT_HEADER		=> false,
	CURLOPT_FOLLOWLOCATION	=> true,
	CURLOPT_RETURNTRANSFER	=> true,
	CURLOPT_CONNECTTIMEOUT	=> 30,
	CURLOPT_TIMEOUT		=> 30,
	CURLOPT_MAXREDIRS	=> 2
	));

echo curl_exec ($curl);
curl_close ($curl);
?>
