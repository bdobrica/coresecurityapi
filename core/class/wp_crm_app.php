<?php
/**
 * Class handling home-screen apps.
 * Each app is defined in the /themes/wp-crm/static/{slug}.php file.
 * The structure of the {slug}.php file is:
 * <code>
 * <?php
 * &#47;*
 * App Title: string
 * App Description: string
 * App Size: int
 * App Style: string
 * *&#47;
 * ...
 * ?>
 * </code>
*/
class WP_CRM_App extends WP_CRM_Model {
	public static $T = 'apps';
	protected static $K = array (
		'pid',
		'slug',
		'icon',
		'title',
		'description',
		'size',
		'style',
		'clicks'
		);
	protected static $U = array (
		'slug'
		);
	public static $F = array (
		'new' => array (
			),
		'edit' => array (
			),
		'view' => array (
			),
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`slug` varchar(64) NOT NULL DEFAULT \'\' UNIQUE',
		'`icon` varchar(64) NOT NULL DEFAULT \'\'',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`size` int(11) NOT NULL DEFAULT 2',
		'`style` text NOT NULL',
		'`clicks` int(11) NOT NULL DEFAULT 0',
		'`flags` int(11) NOT NULL DEFAULT 0'
		);

	public static function scan () {
		if (file_exists ( TEMPLATEPATH . '/static/' )) {
			if ($d = opendir ( TEMPLATEPATH . '/static/' )) {
				while ($n = readdir ($d)) {
					if (!preg_match ('/\.php$/', $n)) continue;
					if ($f = fopen ( TEMPLATEPATH . '/static/' . $n, 'r')) {
						$app = array (	'slug' => '',
								'title' => '',
								'size' => '',
								'style' => '',
								'icon' => '');
						$flag = 0;
						$key = '';
						while ($l = fgets ($f)) {
							switch ($flag) {
								case 1:
									if (strpos ($l, '*/') === 0) {
										$flag = 2;
										if ($app['slug'] && $app['size']) {
											$wp_crm_app = new WP_CRM_App ($app);
											try {
												$wp_crm_app->save ();
												}
											catch (WP_CRM_Exception $wp_crm_exception) {
												echo "app exists, should update!\n";
												}
											}
										}
									else
									if (strpos ($l, 'App') === 0) {
										$c = strpos ($l, ':');
										if ($c !== FALSE) {
											$key = strtolower (substr ($l, 4, $c - 4));
											$app[$key] = trim (substr ($l, $c + 1));
											}
										}
									else {
										if ($key) $app[$key] .= "\n" . trim ($l);
										}
									break;
								default:
									if (strpos ($l, '/*') === 0) {
										$flag = 1;
										$app['slug'] = str_replace ('.php', '', $n);
										}
								}
							if ($flag == 2) break;
							}
						fclose ($f);
						unset ($app);
						}
					}
				closedir ($d);
				}
			}
		}

	public function render () {
		$out = '';
		if (file_exists ( TEMPLATEPATH . '/apps/' . $this->data['slug'] . '.php' ))
			include ( TEMPLATEPATH . '/apps/' . $this->data['slug'] . '.php' );
				
		return empty ($out) ?
			'<a class="app-link app-size-' . $this->data['size'] . '" href="/' . $this->data['slug'] . '" ><span class="app-title">' . $this->data['title'] . '</span></a>' :
			$out;
		}
	}
?>
