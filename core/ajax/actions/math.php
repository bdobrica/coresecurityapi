<?php

function fn ($n) {
	if ($n == 0) return 0;
	if ($n == 1) return 1;
	$z = str_split("$n");
	$s = 0;
	foreach ($z as $l) $s += ($l == '1') ? 1 : 0;
	return fn($n-1) + $s;
	}

$m = 10000000;

for ($n = 0; $n < $m; $n++) {
	if ("$n" == ''.fn($n).'') echo "$n\n";
	if (floor(100*$n/$m)==100*$n/$m) echo round(100*$n/$m,2)."%\n";
	}
?>
