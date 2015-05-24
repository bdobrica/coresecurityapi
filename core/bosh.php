<?php
/**
 * Test objects method inside the WP_CRM ecosystem.
 */
ini_set ('display_errors', 1);
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(__FILE__)))).'/wp-blog-header.php');
spl_autoload_register (function ($class) {
	$class_file = dirname(__FILE__) . '/class/' . strtolower($class) . '.php';
	if (file_exists($class_file)) include ($class_file);
	});

if( !(isset($_COOKIE['boshJid'])
        &&
    isset($_COOKIE['boshSid'])
        &&
    isset($_COOKIE['boshRid'])
        &&
    isset($_COOKIE['boshUrl']))){

    $boshUrl = 'https://api.acreditate.ro/http-bind/'; // BOSH url
    $domain = 'api.acreditate.ro';                    // XMPP host

    $xmppBosh = new XmppBosh($domain, $boshUrl,  '', false, false);

    $node = 'admin';         // Without @example.com
    $password = 'orange-juice';
    $xmppBosh->connect($node, $password);

    $boshSession = $xmppBosh->getSessionInfo();

    setcookie('boshJid', $boshSession['jid'], 0, '/');
    setcookie('boshSid', $boshSession['sid'], 0, '/');
    setcookie('boshRid', $boshSession['rid'], 0, '/');
    setcookie('boshUrl', $boshSession['url'], 0, '/');

	print_r ($boshSession);
}
?>
<html>
    <head>
        <title>Basic XMPP connection</title>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
        <script type="text/javascript" src='https://raw.github.com/carhartl/jquery-cookie/master/jquery.cookie.js'></script>
        <script type="text/javascript" src='../../js/XmppBosh.js'></script>
        <script type="text/javascript" src='basic.js'></script>
    </head>

    <body>
        <button id="disconnect">Disconnect</button>
        <br>

        <div id="log">
        </div>
    </body>
</html>
