<?php
/**
 * Class for sending emails/sms to other objects attached persons.
 */
class WP_CRM_Contact extends WP_CRM_Model {
	public static $T = 'contacts';
	protected static $K = array (
		'object',
		'recipient',
		'sender',
		'template',
		'subject',
		'message',
		'attachments',
		'stamp',
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`object` int(11) NOT NULL DEFAULT 0',
		'`recipient` text NOT NULL',
		'`sender` int(11) NOT NULL DEFAULT 0',
		'`template` int(11) NOT NULL DEFAULT 0',
		'`subject` text NOT NULL',
		'`message` text NOT NULL',
		'`attachments` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);
	public static $F = array (
		'new' => array (
			'recipients:card' => 'Destinatar(i)',
			'sender:mailer' => 'Expeditor',
			'template:template' => 'Mesaj',
			'attachments:attachment' => 'Atasament(e)'
			),
		'view' => array (
			),
		);

	protected $cards;
	protected $attachments;

	private static function attached ($data = null) {
		$out = array ();
		if (is_object ($data)) $data = array ($data);
		if (is_array ($data) && !empty ($data))
			foreach ($data as $object)
				switch ((string) get_class ($object)) {
					case 'WP_CRM_Invoice':
						$out[] = $object->get ('series');
						break;
					}
		return $out;
		}

	public function __construct ($data = null) {
		$this->cards = WP_CRM_Card::gather ($data);
		$this->attachments = self::attached ($data);

		parent::__construct ($data);
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'recipients':
			case 'cards':
				return $this->cards;
				break;
			case 'attachments':
				return $this->attachments;
				break;
			}
		return parent::get ($key, $opts);
		}

	public function save ($data = null) {
		$recipients = array ();
		$attachments = array ();

		foreach ($_POST as $key => $val) {
			if (preg_match ('/^recipients_/', $key))
				$recipients[] = $val;
			if (preg_match ('/^attachments_/', $key)) {
				$invoice = new WP_CRM_Invoice ($val);
				$attachments[$invoice->get('series').'.pdf'] = $invoice->view (FALSE);
				unset ($invoice);
				}
			}

		$sender = new WP_CRM_Mail ((int) $_POST['sender']);

		if (empty ($recipients)) throw new WP_CRM_Exception (__CLASS__ . ' : No recipients!', WP_CRM_Exception::Saving_Failure);
		foreach ($recipients as $recipient) {
			$sender->send ($recipient, array (
				'subject' => $_POST['subject'],
				'content' => $_POST['message']
				), $attachments);
			}
		}
	}
?>
