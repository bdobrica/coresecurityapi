<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
set_include_path (get_include_path() . PATH_SEPARATOR . WP_CRM_DIR . '/libs');
include (WP_CRM_DIR.'/libs/OFC/OFC_Chart.php');

$min = 100;
$max = 0;

$begin = $_GET['begin'] ? ((int) $_GET['begin']) : strtotime('3 months ago');
$end = $_GET['end'] ? ((int) $_GET['end']) : time();

$wp_crm_invoices = new WP_CRM_List ('invoices', array ('mine', 'between' => $begin.' and '.$end, 'sort' => 'time desc'));
$monthly = array ('number' => array(), 'paying' => array ());
foreach (($wp_crm_invoices->get()) as $wp_crm_invoice) {
	$date = $wp_crm_invoice->get('date');

	if (isset($monthly['number'][date('W/y', $date)])) {
		$monthly['number'][date('W/y', $date)] ++;
		$monthly['paying'][date('W/y', $date)] += $wp_crm_invoice->is('paid') ? 1 : 0;
		}
	else {
		$monthly['number'][date('W/y', $date)] = 1;
		$monthly['paying'][date('W/y', $date)] = $wp_crm_invoice->is('paid') ? 1 : 0;
		}
	}

foreach ($monthly['number'] as $num) $max = $max < $num ? $num : $max;
foreach ($monthly['paying'] as $num) $min = $min > $num ? $num : $min;

ksort($monthly['number']);
ksort($monthly['paying']);

if (date('Y', $begin) != date('Y', $end)) $interval = date('d-m-Y', $begin).' - '.date('d-m-Y', $end);
else
if (date('m', $begin) != date('m', $end)) $interval = date('d-m', $begin).' - '.date('d-m-Y', $end);
else
if (date('d', $begin) != date('d', $end)) $interval = date('d', $begin).' - '.date('d-m-Y', $end);
else
$interval = date('d-m-Y', $begin);
	

$title = new OFC_Elements_Title( 'Weekly ('.$interval.')' );

$line_c = new OFC_Charts_Line();
$line_c->set_values( array_values($monthly['number']) );
$line_c->set_colour ( '#cc0000' );
$line_c->set_width( 2 );

$line_p = new OFC_Charts_Line();
$line_p->set_values( array_values($monthly['paying']) );
$line_p->set_colour ( '#00cc00' );
$line_p->set_width( 2 );

$y = new OFC_Elements_Axis_Y();
$y->set_range( $min, $max, floor(($max - $min) / 10) );

$xl = new OFC_Elements_Axis_X_Label_Set();
$xl->set_labels( array_keys($monthly['number']) );
$x = new OFC_Elements_Axis_X();
$x->set_labels ($xl);


$chart = new OFC_Chart();
$chart->set_title( $title );
$chart->add_element( $line_c );
$chart->add_element( $line_p );
$chart->set_x_axis( $x );
$chart->set_y_axis( $y );

echo $chart->toPrettyString();
?>
