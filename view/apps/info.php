<?php
global
	$current_user,
	$wpdb;
get_currentuserinfo();

$list = new WP_CRM_List ('WP_CRM_Invoice', array ('uid='.$current_user->ID));


if (!$list->is ('empty')) {
	$out .= '<div class="app-slide-wrapper">';
	$out .= '<div class="app-slide-container">';
	$stats = array (
		'value' => 0.00,
		'paid' => 0.00,
		'reminder' => 0.00,
		'all' => 0,
		'registered' => 0
		);

	foreach ($list->get() as $invoice) {
		$a = $invoice->get ('value');
		$b = $invoice->get ('payments');
		$c = $invoice->get ('seats');
		$stats['value'] += $a;
		$stats['paid'] += $b;
		$stats['all'] += $c['all'];
		if ($b > 0.1 * $a) {
			$stats['reminder'] += $a - $b;
			$stats['registered'] += $c['all'];
			}
		}
	$out .= '<span class="app-slide-info-b1"><span class="app-slide-info-label">Potential (lei):</span>' . number_format ($stats['value'], 2) . '</span>';
	$out .= '<span class="app-slide-info-b2"><span class="app-slide-info-label">Incasari (lei):</span>' . number_format ($stats['paid'], 2) . '</span>';
	$out .= '<span class="app-slide-info-b3"><span class="app-slide-info-label">Rest de plata (lei):</span>' . number_format ($stats['reminder'], 2) . '</span>';
	$out .= '<span class="app-slide-info-b3"><span class="app-slide-info-label">Participanti:</span>' . $stats['registered'] . ' <span class="app-slide-info-plain">/ ' . $stats['all'] . '</span></span>';
		//$out .= '<a class="app-slide" href="' . $this->get('slug') . '/' . $product->get() . '"><span class="app-slide-title">' . $product->get('short name') . '</span><span class="app-slide-info"><span class="app-slide-info-highlight">' . $product->get('confirmed clients') . '</span>/' . $product->get('clients') . '</span></a>';
	$out .= '</div>';
	$out .= '</div>';
	}


?>
