	<div class="container">
		<div class="row">
					<div id="content" class="col-sm-12 full">
			<div class="row">
				<div class="login-box">
					
					<div class="header">
						<?php bloginfo ('name'); ?>
					</div>
	<?php if ($current_user->ID) { ?>
					Te-ai inregistrat cu succes pe platforma <?php bloginfo('name'); ?>. Pentru a confirma validitatea adresei de email, un mesaj a fost trimis catre <?php echo $current_user->user_email; ?> in care vei gasi instructiuni pentru activarea contului.
	<?php } else { ?>
					<?php $form->render (TRUE); ?>	
					
	<?php } ?>
					<div class="clearfix"></div>
					<a class="pull-left" href="/reset">Ai uitat parola?</a>
					<a class="pull-right" href="/">Autentifica-te!</a>
					<div class="clearfix"></div>
				</div>
			</div><!--/row-->
		
		</div>	

			
			
				</div><!--/row-->		
	</div><!--/container-->
