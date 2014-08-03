<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

if (!isset($_GET['i'])) die ('ERROR');
if (!is_numeric($_GET['i'])) die ('ERROR');
$image = new WP_CRM_Image ((int) $_GET['i']);

if (isset($_GET['e'])) {
	$image->out ('page', TRUE);
	exit (0);
	}

if (isset($_GET['d'])) {
	$path = $image->get('path');
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Type: " . mime_content_type($path));
	header("Content-Disposition: attachment; filename=\"" . basename($path)."\";" );
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($path));
	ob_clean();
	flush();

	readfile ($path);
	exit (0);
	}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC 
	"-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="https://www.facebook.com/2008/fbml" xml:lang="ro" lang="ro">
	<head>
		<title><?php echo $image->get('title'); ?></title>

		<meta charset="utf-8" />

		<meta name="description" content="<?php echo $image->get('description'); ?>" />
		<meta name="keywords" content="<?php echo implode(', ', $image->get('keywords')); ?>">
		

		<meta property="og:title" content="<?php echo $image->get('title'); ?>" />
		<meta property="og:type" content="image" />
		<meta property="og:image" content="<?php echo $image->get('url'); ?>" />
		<meta property="og:site_name" content="Extreme Training - <?php echo $image->get('title'); ?>" />
		<meta property="og:description" content="<?php echo $image->get('description'); ?>" />

		<style type="text/css">
		body { background: #000; }
		div { text-align: center; }
		</style>
	</head>
	<body>
		<div>
			<?php $image->out ('page', TRUE); ?>
		</div>
	</body>
</html>
