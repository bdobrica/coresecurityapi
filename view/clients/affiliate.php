<div class="wp-crm-view-affiliate-wrap">
	<div class="wp-crm-view-affiliate">
<?php
	$coupon	= strtoupper (trim ($_GET['class']));
	try {
		$coupon = new WP_CRM_Coupon (strtoupper (trim ($_GET['class'])), TRUE);
		try {
			$code = $coupon->decode (trim ($_GET['secret']));

			$list = new WP_CRM_List ('WP_CRM_Invoice', array ('coupon=\'' . $code . '\''));
			if ($list->is ('empty')) {
				echo '<div class="wp-crm-view-affiliate-empty">In acest moment nu sunt inscrieri pentru cuponul <strong>' . $code . '</strong>.</div>';
				}
			else {
				echo '<div class="wp-crm-view-affiliate-full">Inscrierile realizate folosind cuponul <strong>' . $code . '</strong>.</div>';
				$view = new WP_CRM_View ($list, array (
					));
				unset ($view);
				}
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			}
		}
	catch (WP_CRM_Exception $wp_crm_exception) {
		}
?>
	</div>
</div>
