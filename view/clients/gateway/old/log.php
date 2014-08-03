<?php
if ($_SERVER['REMOTE_ADDR'] != '86.120.250.28') die ();

$in = file_get_contents (dirname(__FILE__).'/log');

$out = nl2br ($in);
echo $out;
#$out = preg_split ('/^([0-9:. -]+)$/m', $in, -1,  PREG_SPLIT_DELIM_CAPTURE);
#for ($c = 1; $c<count($out); $c+=2) {
#	}
?>
