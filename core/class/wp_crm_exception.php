<?php
class WP_CRM_Exception extends Exception {
	const Invalid_ID		= 1;
	const Invalid_Assignment	= 2;
	const Forgettable_Object	= 3;
	const Object_Exists		= 4;
	const Saving_Failure		= 5;
	const Unknown_Email		= 6;
	const Unknown_UIN		= 7;
	const Unknown_Object		= 8;
	const Missing_Security		= 9;
	const Event_Misfired		= 10;
	const Action_Failure		= 11;
	const Action_Missing		= 12;
	const Invoiceless_Client	= 13;
	const Missing_Seller		= 14;
	const Missing_Buyer		= 15;
	const Missing_Products		= 16;
	const Invalid_Coupon		= 17;
	const Missing_SRP_Verifier	= 18;
	const Session_Required		= 19;
	const SRP_M_Check_Failed	= 20;
	const Missing_SRP_Key		= 21;
	const Invalid_SRP_Checksum	= 22;
	const Invalid_Username		= 23;
	const Invalid_Session		= 24;
	const Invalid_SRP_Command	= 25;
	
	public function __construct ($code = 0, $message = null) {
		parent::__construct ((string) $message, (int) $code);
		}

	public function get ($key = null) {
		if ($key == 'code') return parent::getCode();
		return parent::getMessage ();
		}
	};
?>
