	<div class="container">
		<div class="row">
			<div id="content" class="col-sm-12 full front-page">
				<div class="row">
					<div class="col-sm-12 full">
					</div>
				</div>
				<div class="row">
					<div class="login-box">
						<div class="poscce">
							<img src="<?php bloginfo('stylesheet_directory'); ?>/images/semnatura-poscce-400.png" />
							Programul Operational Sectorial &laquo;Cresterea Competitivitatii Economice&raquo;<br />
							&laquo;Investitii pentru viitorul dumneavoastra!&raquo;
						</div>
						
						<div class="header">
							<?php bloginfo ('name'); ?>
						</div>
					
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
						
						<div class="footer">
						Proiect cofinantat prin Fondul European de Dezvoltare Regionala
						</div>
					</div>
				</div><!--/row-->
			
			</div>	
		</div><!--/row-->		
	</div><!--/container-->
