<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
set_include_path (get_include_path() . PATH_SEPARATOR . WP_CRM_DIR . '/libs');
include (WP_CRM_DIR.'/libs/OFC/OFC_Chart.php');

$min = 100;
$max = 0;

$wp_crm_invoices = new WP_CRM_List ('invoices', array ('mine', 'when' => '3 months ago', 'where' => 'instr(source,\'cd=\')>0', 'sort' => 'time desc'));
$sources = array ();
foreach (($wp_crm_invoices->get()) as $wp_crm_invoice) {
	$source = $wp_crm_invoice->get('source');
	}



die ('');
$sources['  '] = array ('number' => 0, 'paid' => 0);

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
	$bars_c_v = new OFC_Charts_Bar_Horizontal_Value (0, 0);
	$bars_p_v = new OFC_Charts_Bar_Horizontal_Value (0, 0);

	$bars_c->append_value ($bars_c_v);
	$bars_v->append_value ($bars_p_v);
	


$title = new OFC_Elements_Title( 'Sources' );

$x = new OFC_Elements_Axis_X();
$x->set_range( $min, $max, floor(($max - $min) / 5) );

$xl = new OFC_Elements_Axis_X_Label_Set();
$xl->set_labels( array_reverse(array_keys($sources)) );

$y = new OFC_Elements_Axis_Y();
$y->set_labels( $xl );

$chart = new OFC_Chart();
$chart->set_title( $title );
$chart->add_element( $bars_c );
$chart->add_element( $bars_v );
$chart->set_x_axis( $x );
$chart->set_y_axis( $y );

echo $chart->toPrettyString();
?>
