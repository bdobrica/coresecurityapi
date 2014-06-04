	<div class="container">
		<div class="row">
					<div id="content" class="col-sm-12 full">
			<div class="row">
				<div class="login-box">
					
					<div class="header">
						<?php bloginfo ('name'); ?>
					</div>
				
			<?php if (isset ($awaken)) { ?>
				<?php if ($awaken) { ?>
					Contul de utilizator a fost activat cu succes! Poti folosi formularul de mai jos pentru a te autentifica:
				<?php } else { ?>
					A intervenit o eroare in activarea contului de utilizator! Te rugam sa iei legatura cu un administrator al site-ului. Iti multumim!
				<?php } ?>
					<div class="clearfix"></div>
			<?php } ?>
					
					<?php $form->render (TRUE); ?>

					<a class="pull-left" href="/reset">Ai uitat parola?</a>
					<a class="pull-right" href="/signup">Inregistreaza-te!</a>
					
					<div class="clearfix"></div>				
						
				</div>
			</div><!--/row-->
		
		</div>	

			
			
				</div><!--/row-->		
	</div><!--/container-->
