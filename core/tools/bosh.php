<?php
$rid = 3795461411 + 1520;
$xml = "<body rid='$rid' xmlns='http://jabber.org/protocol/httpbind' sid='19e40ef7d6390e4dbe8b9eee9e0d7214a03b81d5'><presence id='pres:1' xmlns='jabber:client'><priority>1</priority><c xmlns='http://jabber.org/protocol/caps' hash='sha-1' node='https://candy-chat.github.io/candy/' ver='kR9jljQwQFoklIvoOmy/GAli0gA='/></presence><iq type='get' from='admin@api.acreditate.ro' to='lobby@rms.api.acreditate.ro' xmlns='jabber:client' id='2:sendIQ'><query xmlns='http://jabber.org/protocol/disco#info'/></iq><presence to='lobby@rms.api.acreditate.ro/admin' id='pres:3' xmlns='jabber:client'><x xmlns='http://jabber.org/protocol/muc'/><c xmlns='http://jabber.org/protocol/caps' hash='sha-1' node='https://candy-chat.github.io/candy/' ver='kR9jljQwQFoklIvoOmy/GAli0gA='/></presence><iq type='get' from='admin@api.acreditate.ro' xmlns='jabber:client' id='4:sendIQ'><query xmlns='jabber:iq:privacy'><list name='ignore'/></query></iq></body>";
echo $xml . "\n";
$curl = curl_init ('https://api.acreditate.ro/http-bind/');
curl_setopt_array ($curl, array (
	CURLOPT_SSL_VERIFYPEER	=> 0,
	CURLOPT_SSL_VERIFYHOST	=> 0,
	CURLOPT_FOLLOWLOCATION	=> true,
	CURLOPT_HEADER		=> 1,
	CURLOPT_POST		=> 1,
	CURLOPT_RETURNTRANSFER	=> true,
	CURLOPT_POSTFIELDS	=> $xml
	));

$return = curl_exec ($curl);
curl_close ($curl);

echo $return;
?>
