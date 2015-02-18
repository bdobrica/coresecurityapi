<?php
class WP_CRM_Product_Structure extends WP_CRM_Structure {
	/**
	 * The attached database table. No prefix.
	 * @var string
	 */
	public static $T = 'product_structure';
	/**
	 * The parent object class.
	 * @var string
	 */
	public static $ROOT = 'WP_CRM_Product';
	/**
	 * The child object class.
	 * @var string
	 */
	public static $CHILD = 'WP_CRM_Task';
	/**
	 * Pair of (actions, form elements).
	 * Form elements are defined as name[:type] => label, where
	 * 	name is a string containing the database key,
	 *	type is a string containing the type prefixed by :
	 *	label is a string containing the displayed label
	 *	Common types are: text (default), basket, checkbox, radio, select, button, submit, close, textarea, email, password, hidden, label, tos, date, seller, buyer, product, spread, matrix, file
	 * @see WP_CRM_Form::_render()
	 * @var array
	 */
	public static $CHILD_F = array (
		'edit' => array (
			'first_name' => 'Prenume',
			'last_name' => 'Nume',
			'email' => 'E-Mail',
			'phone' => 'Telefon',
			)
		);
	}
?>
