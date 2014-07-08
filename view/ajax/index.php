<?php
define ('WP_CRM_AJAX_PROTECTION', TRUE);

$ajax = $_GET['ajax'] ? $_GET['ajax'] : ($_POST['ajax'] ? $_POST['ajax'] : null);
if (!preg_match ('/^[a-z-]+$/', $ajax)) $ajax = null;
if (!file_exists (dirname (__FILE__) . '/' . $ajax . '.php')) $ajax = null;

if (is_null ($ajax)) die ('{error: 1}');

include (dirname (__FILE__) . '/' . $ajax . '.php');
?>
