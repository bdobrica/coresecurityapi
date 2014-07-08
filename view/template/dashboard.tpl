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
		$books = 0;
		foreach ($list->get () as $product) {
			if ($product->get ('code') == 'MDP001') $books = 1;
			if ($product->get ('code') == 'MDP002') $books = 2;
			if ($product->get ('code') == 'MDP003') $books = 1;
			}

		$view = new WP_CRM_View ($list);
		unset ($view);

		if ($books) { ?>

	<h2>Materiale pentru studiu</h2>
	<div class="row">
		<div class="col-md-3">
			<a href="/wp-content/themes/wp-crm/assets/books/<?php echo $books == 1 ? 'Suport_de_Curs_Manager_de_Proiect.pdf' : 'Suport_de_Curs_Manager_de_Proiect-v1.2.pdf'; ?>" target="_blank">
				<img src="/wp-content/themes/wp-crm/assets/books/covers/Suport_de_Curs_Manager_de_Proiect.png" />
				<label>Suport de Curs Manager de Proiect (iunie-iulie 2014)</label>
			</a>
		</div>
		<div class="col-md-3">
			<a href="/wp-content/themes/wp-crm/assets/books/Effective_Project_Management_Seventh_Edition.pdf" target="_blank">
				<img src="/wp-content/themes/wp-crm/assets/books/covers/Effective_Project_Management_Seventh_Edition.gif" />
				<label>Effective Project Management, 7<sup>th</sup> Edition</label>
			</a>
		</div>
		<div class="col-md-3">
			<a href="/wp-content/themes/wp-crm/assets/books/Project_Management_For_Dummies_Fourth_Edition.pdf" target="_blank">
				<img src="/wp-content/themes/wp-crm/assets/books/covers/Project_Management_For_Dummies_Fourth_Edition.gif" />
				<label>Project Management for Dummies, 4<sup>th</sup> Edition</label>
			</a>
		</div>
		<div class="col-md-3">
			<a href="/wp-content/themes/wp-crm/assets/books/97_Things_Every_Project_Manager_Should_Know.pdf" target="_blank">
				<img src="/wp-content/themes/wp-crm/assets/books/covers/97_Things_Every_Project_Manager_Should_Know.gif" style="height: 225px;" />
				<label>97 Things Every Project Manager Should Know</label>
			</a>
		</div>
	</div>
		
	<?php		}
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
