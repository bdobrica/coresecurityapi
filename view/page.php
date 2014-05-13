<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>
			<?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?>
		</title>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />

<?php wp_head(); ?>
	</head>

	<body>
<?php if (have_posts()) { ?>
		<div class="wp-crm-body">
<?php while (have_posts()) {
		the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<?php the_content(); ?>
<?php		} ?>
		</div>
<?php } ?>
<?php wp_footer(); ?>
	</body>
</html>
