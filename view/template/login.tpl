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
						<h1>Autentificare:</h1>
						<ul class="breadcrumb pull-right">
							<li><a href="index.html">Home</a></li>
							<li class="active">Register of Sign in</li>
						</ul>
					</section>
					<section>
						<div class="row">
							<div class="col-sm-6 col-md-6">
								<div class="login-box">
							<?php if (isset ($awaken)) { ?>
								<?php if ($awaken) { ?>
									<div class="alert alert-success">
										Contul de utilizator a fost activat cu succes! Poti folosi formularul de mai jos pentru a te autentifica:
									</div>
								<?php } else { ?>
									<div class="alert alert-danger">
										A intervenit o eroare in activarea contului de utilizator! Te rugam sa iei legatura cu un administrator al site-ului. Iti multumim!
									</div>
								<?php } ?>
									<div class="clearfix"></div>
							<?php } ?>
									
									<?php $form->render (TRUE); ?>

									<a class="pull-left" href="/reset">Ai uitat parola?</a>
									<a class="pull-right" href="/signup">Inregistreaza-te!</a>
									
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
