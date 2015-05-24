<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
header ('HTTP/1.1 200 OK');

if (isset ($_GET['news'])) {
	$sql = $wpdb->prepare ('SELECT * from `db_einvest`.`wp_posts` WHERE ID=%d;', array ((int) $_GET['news']));
	$_post = $wpdb->get_row ($sql, OBJECT);

	echo '<!-- MODAL_TITLE:' . $_post->post_title . ' -->' . "\n";
	$text = apply_filters ('the_content', $_post->post_content);
	echo $text;
	}
?>
