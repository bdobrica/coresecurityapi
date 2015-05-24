<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-blog-header.php');
include (dirname(dirname(__FILE__)) . '/common.php');

if (isset ($_POST['txn_id']) && isset ($_POST['txn_type'])) {
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);
		$req .= '&' . $key . '=' . $value;
		}

	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	
	$f = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
	if (!$f) {
		}
	else {
		fputs ($f, $header . $req);
		while (!feof ($f)) {
			$res = fgets ($f, 1024);
			if (strcmp ($res, 'VERIFIED') == 0) {
				/** check txn_id against the database */
				}
			}
		fclose ($f);
		}
	}
?>
