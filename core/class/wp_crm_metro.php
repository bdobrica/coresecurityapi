<?php
class WP_CRM_Metro {
	const ByDefault	= 14;
	const Options	= 0;
	const Highlight	= 1;
	const Screen	= 2;
	const App	= 3;
	const Cookie	= 'METRO';
	const Style	= <<<STYLE
/* METRO: Background Colors */
.wp-crm-view-menu-wrap,
.wp-crm-view-menu,
.wp-crm-view-data,
.wp-crm-view-actions:hover,
.wp-crm-view-group button,
.wp-crm-view-window-header { background-color: COLOR1; }

.wp-crm-highlight,
.wp-crm-view-table tbody tr:hover,
.wp-crm-view-table tbody tr:hover td,
.wp-crm-view-actions:hover,
.wp-crm-form input[type="submit"]:hover,
.wp-crm-form input[type="button"]:hover,
.wp-crm-form button:hover,
.wp-crm-view-bar a:hover,
.wp-crm-view-group button:hover,
.wp-crm-side-menu { background-color: COLOR2; }

.wp-crm-view-actions,
.wp-crm-form input[type="submit"],
.wp-crm-form input[type="button"],
.wp-crm-form button,
.wp-crm-view-bar,
.wp-crm-view-bar a,
.wp-crm-view-actions-wrap,
.wp-crm-view-group { background-color: COLOR3; }

.wp-crm-selected,
.wp-crm-view-app { background-color: COLOR4; }

.wp-crm-real-invoice { background-color: #000; color: #fff; }

/* METRO: Font Colors */

.wp-crm-selected a,
.wp-crm-highlight a { color: #fff; }
.app-slide-info-highlight { color: COLOR1; }
.app-slide-info-b1 { color: COLOR1; }
.app-slide-info-b2 { color: COLOR2; }
.app-slide-info-b3 { color: COLOR3; }
.app-slide-info-b4 { color: COLOR4; }
STYLE;

	private $scheme;

	private static $colors = array (
		array ('2E1700', '632F00', '261300', '543A24'),
		array ('4E0000', 'B01E00', '380000', '61292B'),
		array ('4E0038', 'C1004F', '40002E', '662C58'),
		array ('2D004E', '7200AC', '250040', '4C2C66'),
		array ('1F0068', '4617B4', '180052', '423173'),
		array ('001E4E', '006AC1', '001940', '2C4566'),
		array ('004D60', '008287', '004050', '306772'),
		array ('004A00', '199900', '003E00', '2D652B'),
		array ('15992A', '00C13F', '128425', '3A9548'),
		array ('E56C19', 'FF981D', 'C35D15', 'C27D4F'),
		array ('B81B1B', 'FF2E12', '9E1716', 'AA4344'),
		array ('B81B6C', 'FF1D77', '9E165B', 'AA4379'),
		array ('691BB8', 'AA40FF', '57169A', '7F6E94'),
		array ('1B58B8', '1FAEFF', '16499A', '6E7E94'),
		array ('569CE3', '56C5FF', '4294DE', '6BA5E7'),
		array ('00AAAA', '00D8CC', '008E8E', '439D9A'),
		array ('83BA1F', '91D100', '7BAD18', '94BD4A'),
		array ('D39D09', 'E1B700', 'C69408', 'CEA539'),
		array ('E064B7', 'FF76BC', 'DE4AAD', 'E773BD')
		);

	public function __construct ($scheme = null) {
		if ($scheme === 'rand') {
			$this->scheme = rand (0, count(self::$colors) - 1);
			}
		else {
			if (session_id () && isset ($_SESSION[self::Cookie]))
				$this->scheme = $_SESSION[self::Cookie];
			else
				$this->scheme = is_null ($scheme) ? WP_CRM_Metro::ByDefault : ((int) $scheme);
			}
		}

	private function parse ($text, $scheme = null) {
		$colors = $this->color ($scheme);
		$out = $text;
		foreach ($colors as $index => $hexcode)
			$out = str_replace ('COLOR' . ($index + 1), strtolower('#' . $hexcode), $out);
		return $out;
		}

	public function color ($key = null, $opts = null) {
		$key = is_null ($key) ? $this->scheme : ((int) $key);
		$scheme = self::$colors[$key];
		if (is_null ($opts)) return $scheme;
		if (isset ($scheme[$opts])) return '#'.$scheme[$opts];
		return $scheme;
		}

	public function style () {
		return $this->parse (self::Style);
		}
	}
?>
