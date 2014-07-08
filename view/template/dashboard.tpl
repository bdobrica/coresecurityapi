<div class="row">
	<ol class="breadcrumb">
		<li><a href="/">Prima Pagina</a></li>
		<li><a href=""></a></li>
	</ol>
	<h1><small>Bine ai venit, <?php echo $wp_crm_user->get ('first_name'); ?>!</small></h1>
	<div class="alert alert-danger">
		Comenzi:
	</div>
<?php
	$list = new WP_CRM_List ('WP_CRM_Order');
	$view = new WP_CRM_View ($list);
	unset ($view);
?>
	<div class="alert alert-success">
		Resurse:
	</div>
<?php
	$list = new WP_CRM_List ('WP_CRM_Resource');
	$view = new WP_CRM_View ($list);
	unset ($view);
?>
</div><!-- end: row -->
