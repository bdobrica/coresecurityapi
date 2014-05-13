<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$orders = explode ("\n\n", file_get_contents(dirname(dirname(__FILE__)).'/shop/log'));

foreach ($orders as $order) {
	if (preg_match_all('/invoice ([0-9]+)/', $order, $match)) {
		if (!empty($match[1]))
			foreach ($match[1] as $invoice) {
				$products = $wpdb->get_results ($wpdb->prepare ('select * from `'.$wpdb->prefix.'new_basket` where iid=%d;', $invoice));
				if (!empty($products)) {
					$o = 0;
					foreach ($products as $product) {
						$series = wp_crm_extract_series($product->code);
						$number = (int) wp_crm_extract_number($product->code);
						for ($q = 1; $q<=$product->quantity; $q++) {
							if (preg_match ('/p_uin_'.$q.' => ([0-9]+)/', $order, $uin)) {
								echo "INVOICE: $invoice\t$uin[1]\n";
								$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'clients` set iid=%d where iid=1336 and uin=%ld and series=%s and number=%d;', array (
									$invoice,
									$uin[1],
									$series,
									$number
									)));
								}
							else
							if (preg_match ('/p_email_'.$q.' => ([A-Z0-9@_-]+)/', $order, $email)) {
								$uin = $wpdb->get_var ($wpdb->prepare ('select uin from `'.$wpdb->prefix.'persons` where email=%s', $email[1]));
								if ($uin) {
									echo "INVOICE-EMAIL: $invoice\t$uin\n";
									$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'clients` set iid=%d where iid=1336 and uin=%ld and series=%s and number=%d;', array (
										$invoice,
										$uin,
										$series,
										$number
										)));
									}
								}
							}
						$o += $product->quantity;
						}
					}
				}
		}
	}
?>
