<?php
class WP_CRM_Receipt {
	private $ID;
	private $series;
	private $number;
	private $invoice;
	private $value;
	private $date;

	public function __construct ($data) {
		global $wpdb;
		if (is_numeric($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'receipts` where id=%d;', $data);
			$receipt = $wpdb->get_row ($sql);
			$this->ID = $receipt->id;
			$this->series = $receipt->series;
			$this->number = $receipt->number;
			$this->invoice = $receipt->iid;
			$this->value = (float) $receipt->value;
			$this->date = (int) $receipt->stamp;
			}
		else
		if (is_string($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'receipts` where series=%s and number=%d;', wp_crm_extract_series($data), wp_crm_extract_number($data));
			$receipt = $wpdb->get_row ($sql);
			$this->ID = $receipt->id;
			$this->series = $receipt->series;
			$this->number = $receipt->number;
			$this->invoice = $receipt->iid;
			$this->value = (float) $receipt->value;
			$this->date = (int) $receipt->stamp;
			}
		else
		if (is_array($data)) {
			if (is_numeric($data['invoice'])) $data['invoice'] = new WP_CRM_Invoice ((int) $data['invoice']);
			if (is_object($data['invoice']) && is_numeric($data['value'])) {
				$this->series = 'R'.$data['invoice']->get('invoice_series');
				$this->invoice = $data['invoice'];
				$this->value = (float) $data['value'];
				$this->date = $data['date'] ? (int) $data['date'] : time(); 
				}
			}
		}

	public function get ($key) {
		if ($key == 'value') return (float) $this->value;
		if ($key == 'invoice') {
			if (is_object($this->invoice)) return $this->invoice;
			$this->invoice = new WP_CRM_Invoice ($this->invoice);
			return $this->invoice;
			}

		if ($key == 'stamp' || $key == 'time' || $key == 'date') return (int) $this->date;
		if ($key == 'code') return $this->series.str_pad($this->number, 5, 0, STR_PAD_LEFT);
		if ($key == 'series') return $this->series;
		if ($key == 'number') return $this->number;
		return $this->ID;
		}

	public function save () {
		global $wpdb;
		$cache = dirname(dirname(__FILE__)).'/cache/series';
		if ($this->ID) return FALSE;
		if (is_numeric($this->invoice)) $this->invoice = new WP_CRM_Invoice ($this->invoice);

		if (file_exists($cache . '/' . $this->series.'.num'))
			$this->number = intval(file_get_contents($cache.'/'.$this->series.'.num')) + 1;
		else
			$this->number = 1;
		file_put_contents($cache.'/'.$this->series.'.num', $this->number);

		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'receipts` (series,number,value,iid,stamp) values (%s,%d,%f,%d,%d);', array (
			$this->series,
			$this->number,
			$this->value,
			$this->invoice->get('id'),
			$this->date
			));
		if (WP_CRM_Debug) echo "WP_CRM_Receipt::save::sql( $sql )\n";
		$wpdb->query ($sql);
		return TRUE;
		}

	public function __destruct () {
		}
	};
?>