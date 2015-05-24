	<div class="container">
		<div class="row">
			<div id="content" class="col-sm-12 full front-page">
				<div class="row">
					<div class="col-sm-12 full top-menu">
						<div class="et-container clearfix">
						<?php include (dirname (__FILE__) . '/remotemenu.tpl'); ?>
						</div>
					</div>
				</div>
				<div class="container-signin">
					<section class="hgroup">
						<h1>Inregistrare:</h1>
						<ul class="breadcrumb pull-right">
							<li><a href="index.html">Home</a></li>
							<li class="active">Register of Sign in</li>
						</ul>
					</section>
					<section>
						<div class="row">
							<div class="col-sm-6 col-md-6">
								<div class="login-box">
							<?php if ($current_user->ID) { ?>
									<div class="clearfix"></div>
							<?php } else { ?>
									
									<?php $form->render (TRUE); ?>
							<?php } ?>

									<a class="pull-left" href="/reset">Ai uitat parola?</a>
									<a class="pull-right" href="/">Autentificare &raquo;</a>
									
									<div class="clearfix"></div>				
								</div>
							</div>
							<div class="col-sm-6 col-md-6">
							</div>
						</div><!--/row-->
					</section>
				</div><!-- /container-signin -->
			</div>	
		</div><!--/row-->		
	</div><!--/container-->
