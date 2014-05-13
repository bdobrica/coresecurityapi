<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
set_include_path (get_include_path() . PATH_SEPARATOR . WP_CRM_DIR . '/libs');
include (WP_CRM_DIR.'/libs/OFC/OFC_Chart.php');

$min = 100;
$max = 0;

$begin = $_GET['begin'] ? ((int) $_GET['begin']) : strtotime('3 months ago');
$end = $_GET['end'] ? ((int) $_GET['end']) : time();

$wp_crm_invoices = new WP_CRM_List ('invoices', array ('mine', 'between' => "$begin and $end", 'sort' => 'time desc'));
$sources = array ();
foreach (($wp_crm_invoices->get()) as $wp_crm_invoice) {
	$source = $wp_crm_invoice->get('source');
	if (isset($sources[$source['src']])) {
		$sources[$source['src']]['number']++;
		$sources[$source['src']]['paid'] += $wp_crm_invoice->is('paid') ? 1 : 0;
		$sources[$source['src']]['storno'] += $wp_crm_invoice->get('paid value') < 0 ? 1 : 0;
		}
	else $sources[$source['src']] = array ( 'number' => 1, 'paid' => $wp_crm_invoice->is('paid') ? 1 : 0, 'storno' => $wp_crm_invoice->get('paid value') < 0 ? 1 : 0 );
	}
#$sources['  '] = array ('number' => 0, 'paid' => 0);

$bars_c = new OFC_Charts_Bar_Horizontal ();
$bars_c->colour = '#CC0000';
$bars_c->text = 'Inscrisi';
$bars_v = new OFC_Charts_Bar_Horizontal ();
$bars_v->colour = '#00CC00';
$bars_v->text = 'Cumparatori';

foreach ($sources as $source => $info) {
	$bars_c_v = new OFC_Charts_Bar_Horizontal_Value (0, $info['number']);
	$bars_p_v = new OFC_Charts_Bar_Horizontal_Value (0, $info['paid']);

	$bars_c->append_value ($bars_c_v);
	$bars_v->append_value ($bars_p_v);
	
	$min = $min < $info['number'] ? $min : $info['number'];
	$min = $min < $info['paid'] ? $min : $info['paid'];
	$max = $max > $info['number'] ? $max : $info['number'];
	$max = $max > $info['paid'] ? $max : $info['paid'];
	}

#$bars_c_v = new OFC_Charts_Bar_Horizontal_Value (0, 0);
#$bars_p_v = new OFC_Charts_Bar_Horizontal_Value (0, 0);

#$bars_c->append_value ($bars_c_v);
#$bars_v->append_value ($bars_p_v);

if (date('Y', $begin) != date('Y', $end)) $interval = date('d-m-Y', $begin).' - '.date('d-m-Y', $end);
else
if (date('m', $begin) != date('m', $end)) $interval = date('d-m', $begin).' - '.date('d-m-Y', $end);
else
if (date('d', $begin) != date('d', $end)) $interval = date('d', $begin).' - '.date('d-m-Y', $end);
else
$interval = date('d-m-Y', $begin);
	

$title = new OFC_Elements_Title( 'Sources (' . $interval . ')' );

$x = new OFC_Elements_Axis_X();
$x->set_range( $min, $max, floor(($max - $min) / 5) );

$y = new OFC_Elements_Axis_Y();
$y->set_offset (1);
$y->set_labels( array_reverse(array_keys($sources)) );

$chart = new OFC_Chart();
$chart->set_title( $title );
$chart->add_element( $bars_c );
$chart->add_element( $bars_v );
$chart->set_x_axis( $x );
$chart->set_y_axis( $y );

echo $chart->toPrettyString();
?>
