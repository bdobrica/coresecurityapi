<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
#include (dirname(__FILE__).'/card/mobilpay.php');

spl_autoload_register (function ($class) {
	include (dirname(dirname(__FILE__)) . '/class/' . strtolower($class) . '.php');
	});

$page = get_page (8);
$content = apply_filters ('the_content', $page->post_content);
?>
<div class="tos-link-buttons">
	<button class="tos-link-yes">Da, am citit si sunt de acord.</button>
	<button class="tos-link-no">Nu sunt de acord.</button>
</div>
<div class="tos-link-content">
<?php echo $content; ?>
</div>
<div class="tos-link-buttons">
	<button class="tos-link-yes">Da, am citit si sunt de acord.</button>
	<button class="tos-link-no">Nu sunt de acord.</button>
</div>
