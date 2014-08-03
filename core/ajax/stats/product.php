<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
set_include_path (get_include_path() . PATH_SEPARATOR . WP_CRM_DIR . '/libs');
include (WP_CRM_DIR.'/libs/OFC/OFC_Chart.php');

$data = array ('customers' => array (), 'paying' => array ());
$product = new WP_CRM_Product ($_GET['p']);

$min = 100;
$max = 0;

$plans = $product->get('planning');
if (!empty($plans))
	foreach ($plans as $code => $stamp) {
		$c = $product->get ('participants number', $code);
		$p = $product->get ('paying number', $code);
		$min = min (array ($min, $c, $p));
		$max = max (array ($max, $c, $p));
		$data['customers'][] = (int) $c;
		$data['paying'][] = $p;
		}

$title = new OFC_Elements_Title( $product->get('nice name') );

$line_c = new OFC_Charts_Line();
$line_c->set_values( $data['customers'] );
$line_c->set_colour ( '#cc0000' );
$line_c->set_width( 2 );

$line_p = new OFC_Charts_Line();
$line_p->set_values( $data['paying'] );
$line_p->set_colour ( '#00cc00' );
$line_p->set_width( 2 );

$y = new OFC_Elements_Axis_Y();
$y->set_range( $min, $max, ($max - $min) / 10 );


$chart = new OFC_Chart();
$chart->set_title( $title );
$chart->add_element( $line_c );
$chart->add_element( $line_p );
$chart->set_y_axis( $y );

echo $chart->toPrettyString();
?>
