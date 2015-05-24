<?php
/*
App Title: My Courses
App Description:
App Order: 6
App Size: 1
App Style:
App Icon: bullseye
*/

list ($id, ) = explode (':', $_GET['filter'], 2);
if (is_numeric ($id)) {
	$course = new WP_CRM_Course ((int) $id);
	$view = new WP_CRM_View ($course);
	unset ($view);
	}
else {
?>
	<div class="row">
		<div class="col-md-12">
			<form action="" method="">
				<div class="row">
					<div class="col-md-3">
						<label>Cheie Curs:</label>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<input class="form-control input-sm" type="text" placeholder="xxxx-xxxx-xxxx-xxxx-xxxx" name="course-key" />
					</div>
					<div class="col-md-3">
						<button class="btn btn-sm btn-success">Inscrie-te!</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php
	$list = new WP_CRM_List ('WP_CRM_Course');
	$view = new WP_CRM_View ($list, array (
			array (
				'type' => 'column',
				'label' => 'Actiuni',
				'items' => array (
					'view' => array (
						'label' => 'Vezi',
						'type' => 'link',
						'url' => '/course/%d'
						),
					'order' => array (
						'label' => 'Inscrie-te!',
						),
					)
				)
			));
	unset ($view);
	}
?>
