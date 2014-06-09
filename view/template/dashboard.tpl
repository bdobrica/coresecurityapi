<div class="row">
	<ol class="breadcrumb">
		<li><a href="/">Prima Pagina</a></li>
		<li><a href=""></a></li>
	</ol>
	<h1><small>Bine ai venit, <?php echo $wp_crm_user->get ('first_name'); ?>!</small></h1>
	<div class="alert alert-info">
		Alege unul dintre cursurile de mai jos la care vrei sa participi. Te poti inscrie numai intr-una dintre sesiunile afisate mai jos. Si nu uita sa iti actualizezi profilul! Poti apasa <a href="/user"><i class="fa fa-user"></i> aici</a> sau in meniul din stanga.
	</div>
	<?php
	$list = new WP_CRM_List ('WP_CRM_Product');
	$list->sort ('id');
	$view = new WP_CRM_View ($list, array (
			'buy' => 'Inscrie-te!',
			));
	unset ($view);
	
	$list = $wp_crm_user->get ('products');
	$list->sort ('id');

	if ($list->is ('empty')) {
	?>
	<div class="alert alert-warning">
		Foloseste butoanele albastre &laquo;Inscrie-te!&raquo; pentru a alege cursul pe care doresti sa-l urmezi.
	</div>
	<?php } else { ?>

	<hr />	
	<div class="alert alert-warning">
		Mai jos ai lista cursurilor la care te-ai inscris:
	</div>

	<?php
		$view = new WP_CRM_View ($list);
		unset ($view);
		}

	?>
	<hr />
	<!--div class="col-md-6">
		<div class="box">
			<div class="box-header">
				<h2><i class="fa fa-check"></i>Office</h2>
				<div class="box-icon">
					<a href="" class="btn-setting"><i class="fa fa-wrench"></i></a>
					<a href="" class="btn-minimize"><i class="fa fa-chevron-up"></i></a>
					<a href="" class="btn-close"><i class="fa fa-times"></i></a>
				</div>
				<div class="nav nav-tabs">
					<li class="active"><a href="#co">Co</a></li>
				</div>
			</div>

			<div class="box-content">
				<div class="tab-content">
					<div id="co" class="tab-pane active">
					</div>
				</div>
			</div>
		</div>
	</div-->
	<div class="alert alert-success">
	Platforma de inscriere este inca in curs de dezvoltare. Urmareste-ne zilnic pentru a vedea noi facilitati.
	</div>
</div><!-- end: row -->
