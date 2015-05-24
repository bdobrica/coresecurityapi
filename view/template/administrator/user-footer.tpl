				</div>
					<div class="col-lg-2 col-md-2 col-sm-1" id="feed">
						<h2>Notificari <a class="fa fa-repeat"></a></h2>

						<ul id="filter">
						<li><a class="red" href="#" data-option-value="notice">Notificari</a></li>
						<li><a class="blue" href="#" data-option-value="message">Mesaje</a></li>
						<li><a href="#" data-option-value="all">Toate</a></li>
						</ul>

<?php $notices = new WP_CRM_List ('WP_CRM_Notice'); 
if (!$notices->is ('empty')) { ?>
						<ul id="timeline">
<?php foreach ($notices->get() as $notice) { ?>
						<li class="notice">
						<i class="fa fa-check-square green"></i>
						<div class="title"><?php echo $notice->get ('title'); ?></div>
						<div class="desc"><?php echo $notice->get ('description'); ?></div>
						<span class="date"><?php echo date ('d-m-Y H:i', $notice->get ('stamp')); ?></span>
						<span class="separator">•</span>
						<span class="name"></span>
						</li>
<?php } ?>
						</ul>
<?php } ?>
					</div>
				<!-- end: Feed -->
				</div>
			</div>
		<!-- end: Content -->
		</div>
		<!--/row-->
	</div>
	<!--/container-->
	
	<div class="modal fade" id="myModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Modal title</h4>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div class="clearfix"></div>

	<footer>
		
		<div class="row">
			
			<div class="col-sm-5">
			</div><!--/.col-->
			
			<div class="col-sm-7 text-right">
			</div><!--/.col-->	
			
		</div><!--/.row-->	

	</footer>
