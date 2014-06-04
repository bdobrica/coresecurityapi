<div class="row">
	Alege unul dintre cursurile de mai jos la care vrei sa participi. Te poti inscrie numai intr-una dintre sesiunile afisate mai jos.
	<?php
	$list = new WP_CRM_List ('WP_CRM_Product');
	$list->sort ('id');
	$view = new WP_CRM_View ($list, array (
			'buy' => 'Inscrie-te!',
			));
	unset ($view);
	?>
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
	Platforma de inscriere este inca in curs de dezvoltare. Urmareste-ne zilnic pentru a vedea noi facilitati.
</div><!-- end: row -->
