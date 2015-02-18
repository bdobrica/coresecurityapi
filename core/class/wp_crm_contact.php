<?php
/**
 * Class for sending emails/sms to other objects attached persons.
 */
class WP_CRM_Contact extends WP_CRM_Model {
	public static $T = 'contacts';
	protected static $K = array (
		'object',
		'recipients',
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
		'`recipients` text NOT NULL',
		'`sender` int(11) NOT NULL DEFAULT 0',
		'`template` int(11) NOT NULL DEFAULT 0',
		'`subject` text NOT NULL',
		'`message` text NOT NULL',
		'`attachments` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);
	public static $F = array (
		'new' => array (
			'recipients:hidden' => 'Destinatar(i)',
			'sender:mailer' => 'Expeditor',
			'template:template' => 'Mesaj',
			'subject' => 'Subiect',
			'message:rte' => 'Mesaj',
			'attachments:attachment' => 'Atasament(e)',
			'stamp:datetime' => 'Data'
			),
		'view' => array (
			'sender:mailer' => 'Expeditor',
			'subject' => 'Subiect',
			'message:rte' => 'Mesaj',
			'attachments:attachment' => 'Atasament(e)',
			'stamp:datetime' => 'Data'
			),
		);

	/**
	 * VERS: 1.0.1
	 * SQL: alter table api_contacts change column recipient recipients text not null;
	 */

	protected $cards;
	protected $recipients;
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
		//$this->cards = WP_CRM_Card::gather ($data);
		//$this->attachments = self::attached ($data);
		$this->recipients = null;
		$this->attachments = null;

		if (is_array ($data) && !empty ($data) && isset ($data[0]) && is_object ($data[0])) {
			$this->recipients = new WP_CRM_Group ($data);
			$this->data['recipients'] = $this->recipients->pack();
			}

		parent::__construct ($data);

		if (is_null ($this->recipients) && isset ($this->data['recipients']) && !empty ($this->data['recipients'])) {
			$this->recipients = new WP_CRM_Group ($this->data['recipients']);
			}

		if (is_null ($this->attachments) && isset ($this->data['attachments']) && !empty ($this->data['attachments'])) {
			$this->attachments = new WP_CRM_Group ($this->data['attachments']);
			}
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'cards':
				return $this->data['recipients'];
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

		if (!is_null($data)) $this->set ($data);

		/*
		$sender = new WP_CRM_Mail ((int) $_POST['sender']);

		
		if (empty ($recipients)) throw new WP_CRM_Exception (__CLASS__ . ' : No recipients!', WP_CRM_Exception::Saving_Failure);
		foreach ($recipients as $recipient) {
			$sender->send ($recipient, array (
				'subject' => stripslashes ($_POST['subject']),
				'content' => stripslashes ($_POST['message'])
				), $attachments);
			}
		*/
		
		parent::save ();
		}
	}
?>
