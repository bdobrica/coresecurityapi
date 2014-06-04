<?php
class WP_CRM_Mail extends WP_CRM_Model {
	const Debug = false;
	public static $T = 'mails';
	protected static $K = array (
		'oid',
		'cid',
		'secure',
		'host',
		'port',
		'name',
		'username',
		'password',
		'flags'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`secure` enum(\'none\',\'ssl\') NOT NULL DEFAULT \'none\'',
		'`host` varchar(128) NOT NULL DEFAULT \'\'',
		'`port` int(5) NOT NULL DEFAULT 0',
		'`name` text NOT NULL',
		'`username` varchar(128) NOT NULL DEFAULT \'\'',
		'`password` varchar(32) NOT NULL DEFAULT \'\'',
		'`flags` int(11) NOT NULL DEFAULT 0'
		);
	public static $F = array (
		'new' => array (
			'cid:company' => 'Companie',
			'name' => 'Nume',
			'secure' => 'SSL',
			'host' => 'Server Mail',
			'port' => 'Port Server',
			'username' => 'Utilizator',
			'password' => 'Parola'
			),
		'edit' => array (
			'cid:company' => 'Companie',
			'name' => 'Nume',
			'secure' => 'SSL',
			'host' => 'Server Mail',
			'port' => 'Port Server',
			'username' => 'Utilizator',
			'password' => 'Parola'
			),
		'view' => array (
			'cid:company' => 'Companie',
			'name' => 'Nume',
			'secure' => 'SSL',
			'host' => 'Server Mail',
			'port' => 'Port Server',
			'username' => 'Utilizator',
			'password' => 'Parola'
			),
		'extended' => array (
			),
		'private' => array (
			)
		);

	private $interface;

	public function __construct ($data = null) {
		$this->interface = new PHPMailer (true);
		$this->interface->IsSMTP ();

		if (is_null ($data)) {
			$admins = get_users (array ('role' => 'administrator', 'orderby' => 'ID', 'number' => 1));

			if (empty ($admins)) throw new WP_CRM_Exception (0);
			$admin = $admins[0];

			$wp_crm_offices = get_user_meta ($admin->ID, '_wp_crm_offices', TRUE);
			$wp_crm_office_query = is_numeric ($wp_crm_offices) ? sprintf ('oid=%d', $wp_crm_offices) : (!empty($wp_crm_offices) ? sprintf ('oid in (%s)', implode (',', $wp_crm_offices)) : '');
			$wp_crm_mails = new WP_CRM_List ('WP_CRM_Mail', $wp_crm_office_query ? array ($wp_crm_office_query) : null);

			if ($wp_crm_mails->is ('empty')) throw new WP_CRM_Exception (0);
			$wp_crm_mail = $wp_crm_mails->get ('first');

			$data = (int) $wp_crm_mail->get ();
			}

		parent::__construct ($data);

		$this->interface->Host		= $this->data['host'];
		$this->interface->SMTDebug	= WP_CRM_Mail::Debug;
		$this->interface->SMTPAuth	= true;
		$this->interface->SMTPSecure	= $this->data['secure'];
		$this->interface->Port		= $this->data['port'];
		$this->interface->Username	= $this->data['username'];
		$this->interface->Password	= $this->data['password'];

		$this->interface->SetFrom ($this->data['username'], $this->data['name']);
		$this->interface->AddReplyTo ($this->data['username'], $this->data['name']);
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'mail':
			case 'email':
			case 'e-mail':
				return $this->data['username'];
				break;
			}
		return parent::get ($key, $opts);
		}

	public function send ($to, $message, $attachments = null) {
		/*
		TODO: check if $message is an instance for Template object
		*/
		if (is_object($message) && ($message instanceof WP_CRM_Template)) {
			$this->interface->Subject	= $message->get ('subject');
			$this->interface->MsgHTML	($message->get ('content'));
			}
		else
		if (is_array($message)) {
			$this->interface->Subject	= $message['subject'];
			$this->interface->MsgHTML	($message['content']);
			}

		$this->interface->AddAddress ($to);

		if (is_string($attachments) && !empty($attachments))
			$attachments = array ($attachments);

		if (!empty($attachments))
			foreach ($attachments as $name => $path)
				$this->interface->AddAttachment ($path, $name);
		try {
			$this->interface->Send ();
			}
		catch (phpmailerException $e) {
			echo $e->getMessage ();
			}
		}
	}
?>
