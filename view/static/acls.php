<?php
/*
App Title: Permissions
App Parent: system
App Order: 3
App Description:
App Size: 1
App Style:
App Icon: shield
*/
?>
<form action="" method="post">
	<table>
		<tbody>
			<tr>
				<th>
				</th>
<?php
$roles = $wp_crm_user->get ('role_list');
foreach ($roles as $key => $value) {
?>
				<th>
					&nbsp; <?php echo str_replace('WP CRM ', '', $value); ?> &nbsp;
				</th>
<?php
	}
?>
			</tr>
<?php
$capabilities = $wp_crm_user->get ('capability_list');
foreach ($capabilities as $key => $value) {
?>
			<tr>
				<th><?php echo ucwords (str_replace (array ('wp_crm_', '_'), array ('', ' '), $key)); ?></th>
<?php
	foreach ($roles as $_key => $_value) {
?>
				<td style="text-align: center;"><input type="checkbox" value="" /></td>
<?php
		} // end foreach
?>
			</tr>
<?php
	} // end foreach
?>
		</tbody>
	</table>
</form>
