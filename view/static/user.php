<?php
/*
App Title: Profil
App Description:
App Size: 1
App Style:
App Order: 1
App Icon: user 
*/

?>
<div class="row">
	<div class="col-sm-2">
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
/*
$structure = new WP_CRM_Form_Structure ($wp_crm_person);
$form = new WP_CRM_Form ($structure);
$form->set ('state', $wp_crm_state->get());

if ($_POST['object']) $form->action ();
$form->render (TRUE);
*/
?>
		<div class="row">
			<div class="col-xs-7 col-sm-12">
				<img class="profile-image" src="<?php echo $wp_crm_person->get ('avatar', '500x500'); ?>" />
			</div>
			<div class="col-xs-5 col-sm-12">
				<h3><?php echo $wp_crm_person->get ('name'); ?> <span class="wp-crm-view-actions wp-crm-view-edit pull-right fa fa-edit" rel="<?php echo $wp_crm_person->get ('self'); ?>"></span></h3>
				<h3><span class="fa fa-gift"></span> Invitatii (<span class="wp-crm-view-var-invitations"><?php echo $wp_crm_user->get ('defaults', 'promoter_invitations'); ?></span>)</h3>
				<div>
					Foloseste butonul de mai jos pentru a-ti invita prietenii sa devina membrii in clubul exclusivist al clientilor E-Invest Marketing. In acest fel, vei putea deveni <span style="color: #47a447;">Manager de Cont E-Invest</span> obtinand beneficii majore, asa cum <a href="http://einvest.ro/consultanta/manageri-de-cont/" target="_blank" style="color: #47a447;">am descris aici</a>. Poti trimite cel mult 5 invitatii.
					<button class="upd-invitation wp-crm-view-actions wp-crm-view-invitation btn btn-sm btn-block btn-success" rel="WP_CRM_Invitation-0"<?php if ($wp_crm_user->get ('defaults', 'promoter_invitations') < 1) echo ' disabled'; ?>><i class="fa fa-envelope"></i> Trimite</button>
				</div>
				<h3><span class="fa fa-building-o"></span> Companii</h3>
				<div>
<?php
$companies = $wp_crm_user->get ('company_list');
if (!$companies->is ('empty')) {
	$view = new WP_CRM_View ($companies, array (
		array (
			'type' => 'fields',
			'headerless' => TRUE,
			'items' => array (
				'name' => 'Companie',
				)
			),
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
				'edit' => array (
					'label' => 'Modifica'
					)
				)
			)
		));
	unset ($view);
	}
else {
?>
					In acest moment nu ai asociate entitati juridice cu contul tau. Foloseste butonul de mai jos pentru a adauga companii, organizatii non-guvernamentale sau unitati administrativ teritoriale pe care le reprezinti si vei beneficia de avantajele acestei platforme la adevaratul potential.
<?php } ?>
					<button class="wp-crm-view-actions wp-crm-view-add btn btn-sm btn-block btn-primary" rel="WP_CRM_Company-0"><i class="fa fa-plus"></i> Adauga Companie</button>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-10">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-news">Intelligence</a></li>
			<li><a href="#tab-products">Servicii Oferite</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab-news">
				<div class="row">
					<div class="col-lg-6">
						<div class="box">
							<div class="box-header">
								<h2><i class="fa fa-inbox"></i>Comunicari</h2>
							</div>
							<div class="box-content inbox">
<?php

$sql = 'SELECT DISTINCT wposts.* 
FROM `db_acreditate`.`wp_posts` wposts
	LEFT JOIN `db_acreditate`.`wp_term_relationships` wptermrels ON (wposts.ID = wptermrels.object_id)
	LEFT JOIN `db_acreditate`.`wp_term_taxonomy` wptaxonomy ON (wptermrels.term_taxonomy_id = wptaxonomy.term_taxonomy_id)
WHERE 
	wptaxonomy.taxonomy = \'category\'
	AND wptaxonomy.term_id=3
	AND wposts.post_status in (\'publish\',\'private\')
	AND wposts.post_type = \'post\'
	AND wposts.post_date < NOW()
ORDER BY wposts.post_status,wposts.post_date DESC
LIMIT 20';

#$sql = 'SELECT * from `db_einvest`.`wp_posts` WHERE `wp_posts`.post_status in (\'publish\',\'private\') AND `wp_posts`.post_type = \'post\' AND `wp_posts`.post_date < NOW() ORDER BY `wp_posts`.post_status,`wp_posts`.post_date DESC LIMIT 20;';
$_posts = $wpdb->get_results ($sql, OBJECT);
foreach ($_posts as $_post) {
	$priv = $_post->post_status == 'private' ? TRUE : FALSE;
?>
							<div class="message">
								<div class="message-title<?php echo $priv ? ' red' : ''; ?>" style="font-size: 16px;">
								<span class="fa <?php echo $priv ? 'fa-star' : 'fa-bookmark'; ?>"></span> <?php echo apply_filters ('the_title', $_post->post_title); ?>
								</div>
								<div class="header">
									<img class="avatar" src="<?php bloginfo('stylesheet_directory'); ?>/assets/img/e-invest-logo.png" style="height: 34px; width: auto;" />
									<div class="from">
										<span>E-Invest Marketing</span>
										<a href="http://www.einvest.ro">www.einvest.ro</a>
									</div>
									<div class="date" style="padding-right: 5px;">
									<?php echo apply_filters ('the_date', $_post->post_date); ?> <span class="fa fa-calendar"></span>
									</div>
								</div>
								<div class="message-content">
<?php
	$text = strip_shortcodes ($_post->post_content);
	$text = apply_filters ('the_content', $text);
	$text = str_replace (']]>', ']]&gt;', $text);
	$len = apply_filters ('excerpt_length', 55);
	$more = apply_filters ('excerpt_more', ' [...]');
	echo wp_trim_words ($text, $len, $more);
?>
									<div class="clearfix" style="text-align: right; padding: 5px 20px 15px;">
										<a href="#" class="btn btn-sm btn-success wp-crm-view-actions wp-crm-view-intelligence" rel="<?php echo $_post->ID; ?>"><i class="fa fa-eye"></i> Detalii</a>
									</div>
								</div>
							</div>
<?php
	}
?>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="box calendar">
							<div class="box-header">
								<h2><i class="fa fa-calendar"></i>Calendar</h2>
							</div>
							<div class="box-content">
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
									</div>		
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tab-products">
<?php
$list = new WP_CRM_List ('WP_CRM_Product');
$view = new WP_CRM_View ($list, array (
	array (
		'type' => 'column',
		'label' => '',
		'items' => array (
			'view' => array (
				'label' => 'Detalii',
				),
			)
		),
	array (
		'type' => 'column',
		'label' => '',
		'items' => array (
			'order' => array (
				'label' => 'Achizitioneaza',
				),
			)
		)
	));
unset ($view);
?>
			</div>
		</div>
<?php
/**
$wp_crm_office_filter = sizeof ($wp_crm_offices) == 1 ? sprintf ('id=%d', current($wp_crm_offices)) : sprintf ('id in (%s)', implode (',', $wp_crm_offices));
$offices = new WP_CRM_List ('WP_CRM_Office', array ($wp_crm_office_filter ? : 'uid=' . $current_user->ID));

if ($offices->is ('empty')) {
	$office = new WP_CRM_Office (array ('name' => $current_user->user_email));
	try {
		$office->save ();

		$list = new WP_CRM_List ('WP_CRM_Company', array (sprintf ('oid=%d', $office->get ())));
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
	catch (WP_CRM_Exception $wp_crm_exception) {
		}
	}
else
	foreach ($offices->get () as $office) {
		$list = new WP_CRM_List ('WP_CRM_Company', array (sprintf ('oid=%d', $office->get ())));
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
*/
?>
	</div>
</div>
