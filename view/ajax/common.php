<?php
header ('HTTP/1.1 200 OK');
$current_user = wp_get_current_user ();
if (!$current_user->ID) die ('UNAUTHORIZED: "Access is denied due to invalid credentials."');
?>
