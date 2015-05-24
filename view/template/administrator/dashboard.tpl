<div class="row">
	<div class="col-sm-12" style="">
		<div style="text-align: center;"><img src="<?php bloginfo('stylesheet_directory'); ?>/assets/img/poscce.jpg" style="width: 450px; margin: 10px auto; clear: both;" /></div>
<br />
Proiectul &quot;Implementarea unor sisteme informatice de e-learning si comert electronic in cadrul societatii E-Invest Marketing, in scopul cresterii productivitatii firmei&quot; a beneficiat de finantare in cadrul Programului Operational Sectorial &quot;Cresterea Competitivitatii Economice&quot;, Axa Prioritara III &quot;Tehnologia Informatiei si Comunicatii pentru Sectoarele Privat si Public&quot;, Domeniul Major de Interventie 3: Sustinerea E-Economiei, Operatiunea 2 &quot;Sprijin pentru Dezvoltarea Sistemelor de Comert Electronic si a Altor Solutii On-Line pentru Afaceri&quot;
<br />
<br />

<strong>COD SMIS 46694</strong><!--more-->
<br />
<br />

<hr />
	</div>
</div>
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
								<div class="day"><?php
echo str_replace (array (
	'Monday',
	'Tuesday',
	'Wednesday',
	'Thursday',
	'Friday',
	'Saturday',
	'Sunday'
	), array (
	'Luni',
	'Marti',
	'Miercuri',
	'Joi',
	'Vineri',
	'Sambata',
	'Duminica'
	), date ('l')); ?></div>
								<div class="date"><?php echo str_replace ('March', 'Martie', date('j F')); ?></div>
								<ul class="events">
								</ul>
								<div class="add-event">
									<a href="#" class="btn btn-small btn-block btn-primary wp-crm-view-actions wp-crm-view-add" rel="WP_CRM_Entry-0"><i class="fa fa-plus"></i> Adauga Eveniment Public</a>
								</div>		
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="wp-crm-ude-calculator"></div>
		<div class="row">
<?php
$projects = new WP_CRM_List ('WP_CRM_Project');
$values = array ();
$number = 0;
$budget = 0;
$cofinancing = 0;
if (!$projects->is ('empty'))
foreach ($projects->get () as $project) {
	$values[] = $project->get ('budget');
	$budget += $project->get ('budget');
	$cofinancing += $project->get ('cofinancing');
	}
$number = count ($values);
if ($number > 10) array_splice ($values, 0, -10);
?>
			<div class="col-sm-6">
				<div class="smallstat box">
					<div class="boxchart-overlay green">
						<div class="boxchart"><?php echo implode(',', $values); ?></div>
					</div>	
					<span class="title">Clienti</span>
					<span class="value"><?php echo $number; ?></span>
					<a href="/company" class="more">
						<span>Detalii</span>
						<i class="fa fa-chevron-right"></i>
					</a>	
				</div>
			</div>
<?php
$invoices = new WP_CRM_List ('WP_CRM_Invoice');
$values = array ();
if (!$invoices->is ('empty'))
foreach ($invoices->get () as $invoice) $values[] = $invoice->get ('value');
$number = count ($values);
if ($number > 10) array_splice ($values, -10);
?>
			<div class="col-sm-6">
				<div class="smallstat box">
					<div class="linechart-overlay red">
					<div class="linechart"><?php echo implode (',', $values); ?></div>
					</div>	
					<span class="title">Facturi</span>
					<span class="value"><?php echo $number; ?></span>
					<a href="/invoices" class="more">
						<span>Detalii</span>
						<i class="fa fa-chevron-right"></i>
					</a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="smallstat box">
					<i class="fa fa-usd blue"></i>
					<span class="title">Finantari</span>
					<span class="value"><?php echo $budget; ?></span>
					<a href="/projects" class="more">
						<span>Detalii</span>
						<i class="fa fa-chevron-right"></i>
					</a>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="smallstat box">
					<i class="fa fa-usd red"></i>
					<span class="title">Cofinantari</span>
					<span class="value"><?php echo $cofinancing; ?></span>
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
	<div class="col-sm-12">
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
</div>
