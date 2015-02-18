<?php
define ('WP_CRM_AJAX_PROTECTION', TRUE);

$ajax = isset ($_GET['ajax']) ? $_GET['ajax'] : (isset ($_POST['ajax']) ? $_POST['ajax'] : null);
if (!preg_match ('/^[a-z-]+$/', $ajax)) $ajax = null;
if (strpos ($ajax, 'un') === 0) { $ajax = substr ($ajax, 2); $_GET['negate'] = TRUE; }
if (!file_exists (dirname (__FILE__) . '/' . $ajax . '.php')) $ajax = null;

if (is_null ($ajax)) die ('{error: 1}');

include (dirname (__FILE__) . '/' . $ajax . '.php');
?>
