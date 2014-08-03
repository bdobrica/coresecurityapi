<?php
class WP_CRM_Participants extends WP_CRM_Invoice {
	private $list;

	public static $F = array (
		'new' => array (
			'ID' => 'Factura',
			'list:spread' => 'Participanti'
			),
		'opts' => array (
			'card' => 'Card#',
			'person_id' => 'ID',
			'package' => 'Pachet',
			'first_name' => 'Prenume',
			'last_name' => 'Nume',
			'phone'	=> 'Telefon',
			'email' => 'E-Mail'
			)
		);

	public function __construct ($data = null) {
		global $wpdb;

		parent::__construct ($data);

		$this->list = array ();
		if ($this->ID) {
			$sql = $wpdb->prepare ('select pid,code,product,quantity from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where iid=%d order by code;', array (
				$this->ID
				));

			$products = $wpdb->get_results ($sql);
			if ($products)
			foreach ($products as $product) {
				$clients = new WP_CRM_List ('WP_CRM_Client', array (
					'iid=' . $this->ID,
					'pid=' . $product->pid
					));
				$this->list[$product->code] = array (
					'product_id' => $product->pid,
					'product' => $product->product,
					'quantity' => $product->quantity,
					'clients' => $clients->get ()
					);
				}
			}
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'list':
				return $this->list;
				break;
			}
		return $this->ID;
		}

	private static function table ($cols, $rows, $class = '') {
		$out .= '<table class="' . $class . '-client-table">' . "\n";
		$out .= "\t<thead>\n";
		$out .= "\t\t<tr>\n";
		foreach ($cols as $col) {
			$out .= "\t\t\t" . '<th class="' . $class . '-client-cell">' . $col . '</th>' . "\n";
			}
		$out .= "\t\t</tr>\n";
		$out .= "\t</thead>\n";
		$out .= "\t<tbody>\n";
		foreach ($rows as $row) {
			$out .= "\t\t<tr>\n";
			foreach ($cols as $key => $col)
				$out .= "\t\t\t" . '<td class="' . $class . '"-client-cell>' . $row[$key] . '</td>' . "\n";
			$out .= "\t\t</tr>\n";
			}
		$out .= "\t</tbody>\n";
		$out .= '</table>' . "\n";
		return $out;
		}

	public function view ($class = '', $echo = FALSE) {
		$cols = array_merge (array ('#'), array_values (self::$F['opts']));
		$rows = array ();

		if (!empty ($this->list))
			foreach ($this->list as $product => $data)
				if (!empty ($data['clients'])) {
					$c = 0;
					foreach ($data['clients'] as $client) {
						$c++;
						$row = array ($product);
						foreach (self::$F['opts'] as $key => $name)
							$row[] = '<input class="' . $class . '-client-data" name="client-data-' . $product . '-' . $c . '" value="' . $client->get ($key) . '" type="text" />';
						$rows[] = $row;
						}
					while ($c < $data['quantity']) {
						$c++;
						$row = array ($product);
						foreach (self::$F['opts'] as $key => $name)
							$row[] = '<input class="' . $class . '-client-data" name="client-data-' . $product . '-' . $c . '" value="" type="text" />';
						$rows[] = $row;
						}
					}
				else {
					$c = 0;
					while ($c < $data['quantity']) {
						$c++;
						$row = array ($product);
						foreach (self::$F['opts'] as $key => $name)
							$row[] = '<input class="' . $class . '-client-data" name="client-data-' . $product . '-' . $c . '" value="" type="text" />';
						$rows[] = $row;
						}
					}

		$out = '';
		$out .= self::table ($cols, $rows, $class);
		if (!$echo) return $out;
		echo $out;
		}

	private static function _strip_keys ($array, $prefix) {
		if (empty($array)) return array ();
		$new = array ();
		foreach ($array as $key => $value) if (strpos ($key, $prefix) !== FALSE) $new[str_replace ($prefix, '', $key)] = $value;
		return $new;
		}

	public function save () {
		if (!empty ($this->list))
			foreach ($this->list as $product => $data) {
/*
 * TODO: should check the new list versus the current list. some clients
 *	 should be removed.
 */
				for ($c = 0; $c<$data['quantity']; $c++) {
					$post = self::_strip_keys ($_POST, 'client-data-' . $product . '-' . ($c+1) . '-');

					if ($post['person_id']) {
						$person = new WP_CRM_Person ((int) $post['person_id']);
						unset ($post['person_id']);
						$person->set ($post);
						}
					else {
						unset ($post['person_id']);
						$person = new WP_CRM_Person ($post);
						$person->save ();
						$client = new WP_CRM_Client ($person);
						$client->register ($data['product_id'], $this->ID);
						}
					}
				}
		}
	}
?>
