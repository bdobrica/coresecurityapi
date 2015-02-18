<?php
/*
App Title: Companii
App Description:
App Size: 1
App Style:
App Icon: gears 
*/

?>
<div class="row">
	<div class="col-sm-4">
<?php
try {
	$wp_crm_person = new WP_CRM_Person ($current_user->user_email);
	}
catch (WP_CRM_Exception $wp_crm_exception) {
	$wp_crm_person = new WP_CRM_Person (array (
		'email' => $current_user->user_email
		));
	$wp_crm_person->save ();
	}

$structure = new WP_CRM_Form_Structure ($wp_crm_person);
$form = new WP_CRM_Form ($structure);
$form->set ('state', $wp_crm_state->get());

if ($_POST['object']) $form->action ();
$form->render (TRUE);
?>
	</div>
	<div class="col-sm-4">
		<div class="box calendar">	
			<div class="calendar-small"></div>
			<div class="calendar-details">
				<div class="day">MARTI</div>
				<div class="date">20 IANUARIE</div>
				<ul class="events">
					<li>20 IANUARIE, 19:30 Meeting</li>
					<li>20 IANUARIE, 19:30 Meeting</li>
				</ul>
				<div class="add-event">
					<input type="text" class="new event" placeholder="Adauga un nou eveniment.">
				</div>		
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="row">
			<div class="col-sm-6">
				<div class="smallstat box">
					<div class="boxchart-overlay green">
						<div class="boxchart">5,6,7,2,0,4,2,4,8,2,3,3,2</div>
					</div>	
					<span class="title">Clienti</span>
					<span class="value">15</span>
					<a href="/company" class="more">
						<span>Detalii</span>
						<i class="fa fa-chevron-right"></i>
					</a>	
				</div>
			</div>
			<div class="col-sm-6">
				<div class="smallstat box">
					<div class="linechart-overlay red">
					<div class="linechart">1,2,6,4,0,8,2,4,5,3,1,7,5</div>
					</div>	
					<span class="title">Facturi</span>
					<span class="value">10</span>
					<a href="/invoices" class="more">
						<span>Detalii</span>
						<i class="fa fa-chevron-right"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
<?php
$offices = new WP_CRM_List ('WP_CRM_Office', array (is_numeric ($wp_crm_offices) ? sprintf ('id=%d', $wp_crm_offices) : (!empty($wp_crm_offices) ? sprintf ('id in (%s)', implode (',', $wp_crm_offices)) : 1)));

if ($offices->is ('empty')) {
	$office = new WP_CRM_Office (array ('name' => $current_user->user_email));
	$office->save ();

	$list = new WP_CRM_List ('WP_CRM_Company', array ('oid=' . $office->get ()));
	$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'add' => array (
					'label' => 'Organizatie Noua'
					)
				)
			),
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
				'view' => array (
					'label' => 'Vezi'
					),
				'memo' => array (
					'label' => 'Memo'
					),
				'delete' => array (
					'label' => 'Sterge'
					)
				))
		));
	unset ($view);
	}
else
	foreach ($offices->get () as $office) {
		$list = new WP_CRM_List ('WP_CRM_Company', array ('oid=' . $office->get ()));
		$view = new WP_CRM_View ($list, array (
			array (
				'type' => 'toolbar',
				'items' => array (
					'add' => array (
						'label' => 'Organizatie Noua'
						)
					)
				),
			array (
				'type' => 'column',
				'label' => 'Actiuni',
				'items' => array (
					'view' => array (
						'label' => 'Vezi'
						),
					'edit' => array (
						'label' => 'Modifica'
						),
					'memo' => array (
						'label' => 'Memo'
						),
					'delete' => array (
						'label' => 'Sterge'
						)
					))
			));
		unset ($view);
		}
?>
	</div>
	<div class="col-sm-6">
	</div>
</div>
