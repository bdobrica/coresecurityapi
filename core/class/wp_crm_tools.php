<?php
class WP_CRM_Tools {
	public static function proper ($string) {
		$string = strtolower($string);
		if (ord($string[0]) > 96 && ord($string[0]) < 123) $string[0] = chr(ord($string[0])-32);
		for ($c = 1; $c<strlen($string); $c++)
			$string[$c] = ((ord($string[$c-1]) < 97 || ord($string[$c-1]) > 122) && (ord($string[$c-1]) < 65 || ord($string[$c-1]) > 90) && (ord($string[$c]) > 96 && ord($string[$c]) < 123)) ? chr(ord($string[$c])-32) : $string[$c];
		return $string;
		}
	}
?>
