<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");

$c = curl_init ('http://api.einvest.ro:5280/http-poll/');

$xmldata = file_get_contents('php://input');

curl_setopt ($c, CURLOPT_HEADER, 0);
curl_setopt ($c, CURLOPT_POST, 1);
curl_setopt ($c, CURLOPT_POSTFIELDS, $xmldata);
curl_setopt ($c, CURLOPT_FOLLOWLOCATION, true);
curl_setopt ($c, CURLOPT_HTTPHEADER, array (
		'Accept: text/xml',
		'Content-Type: text/xml; charset=utf-8'
		));
curl_setopt ($c, CURLOPT_VERBOSE, 0);
curl_setopt ($c, CURLOPT_RETURNTRANSFER, 1);

$r = curl_exec ($c);
$log = @file_get_contents ('log.txt');
$log .= "\n" . date ('d-m-Y H:i:s') . "\n\n" . $r . "\n===\n";
file_put_contents ('log.txt', $log);
echo $r;
?>
