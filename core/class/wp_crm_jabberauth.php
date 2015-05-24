<?php
/*
Copyright (c) <2005> LISSY Alexandre, "lissyx" <alexandrelissy@free.fr>

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software andassociated documentation files (the "Software"), to deal in the
Software without restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is furnished to do so,
subject to thefollowing conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class WP_CRM_JabberAuth {
	const DateFormat	= "M d H:i:s"; /* Check date() for string format. */

	private $jabber_user;   	/** This is the jabber user passed to the script. filled by $this->command() */
	private $jabber_pass;   	/** This is the jabber user password passed to the script. filled by $this->command() */
	private $jabber_server; 	/** This is the jabber server passed to the script. filled by $this->command(). Useful for VirtualHosts */
	private $jid;           	/** Simply the JID, if you need it, you have to fill. */
	private $data;          	/** This is what SM component send to us. */
	
	private $command;		/** This is the command sent ... */

	private $stdin;   		/* stdin file pointer */
	private $stdout; 		/* stdout file pointer */

	private $user;			/* the wordpress user */

	public function __construct () {
		$this->openstd();
		}
	
	private function stop() {
		$this->closestd(); 	// Simply close files
		exit(0);		// and exit cleanly
		}
	
	private function openstd () {
		$this->stdout = @fopen("php://stdout", "w"); 	// We open STDOUT so we can read
		$this->stdin  = @fopen("php://stdin", "r"); 	// and STDIN so we can talk !
		}
	
	private function readstdin () {
		$l = @fgets($this->stdin, 3); // We take the length of string
		$length = @unpack("n", $l); // ejabberd give us something to play with ...
		$len = $length["1"]; // and we now know how long to read.
		if ($len > 0) { // if not, we'll fill logfile ... and disk full is just funny once
			$data   = @fgets($this->stdin, $len+1);
			// $data = iconv("UTF-8", "ISO-8859-15", $data); // To be tested, not sure if still needed.
			$this->data = $data; // We set what we got.
			$this->logger ("in: " . $data);
			}
		}
	
	private function closestd () {
		@fclose($this->stdin); // We close everything ...
		@fclose($this->stdout);
		}
	
	private function out ($message) {
		@fwrite($this->stdout, $message); // We reply ...
		$this->logger ("out: " . $message);
		}
	
	public function listen () {
		do {
			$this->readstdin (); // get data
			$length = strlen ($this->data); // compute data length
			if ($length > 0) { // for debug mainly ...
				}
			$ret = $this->command(); // play with data !
			$this->out ($ret); // send what we reply.
			$this->data = NULL; // more clean. ...
			}
		while (true);
		}
	
	private function command () {
		$data = $this->splitcomm(); // This is an array, where each node is part of what SM sent to us :
		// 0 => the command,
		// and the others are arguments .. e.g. : user, server, password ...
		$this->logger (print_r ($data, TRUE));	
	
		switch($data[0]) {
			case 'isuser': // this is the "isuser" command, used to check for user existance
				$this->jabber_user = $data[1];
				$return = $this->checkuser ();
				break;
				
			case 'auth': // check login, password
				$this->jabber_user = $data[1];
				$this->jabber_pass = $data[3];
				$return = $this->checkpass();
				break;
				
			case 'setpass':
				$return = FALSE; // We do not want jabber to be able to change password
				break;
				
			default:
				$this->stop(); // if it's not something known, we have to leave.
				break;
			}
			
		$return = ($return) ? 1 : 0;
		return @pack("nn", 2, $return);
		}
	
	private function checkpass () {
		/*
		 * Put here your code to check password
		 * $this->jabber_user
		 * $this->jabber_pass
		 * $this->jabber_server
		 */
		$this->logger ('checkpass: ' . $this->jabber_pass);
		if ($this->checkuser () === FALSE) return FALSE;
		if (!$this->user instanceof WP_CRM_User) return FALSE;
		return $this->user->check ('password', $this->jabber_pass);
		}
	
	private function checkuser() {
		/*
		 * Put here your code to check user
		 * $this->jabber_user
		 * $this->jabber_pass
		 * $this->jabber_server
		 */
		$this->logger ('checkuser: ' . $this->jabber_user);
		try {
			$this->user = new WP_CRM_User ($this->jabber_user);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			$this->user = NULL;
			}
		return is_null ($this->user) ? FALSE : TRUE;
		}
	
	private function splitcomm() {
		/**
		 * simply split command and arugments into an array.
		 */
		return explode(":", $this->data);
		}
	private function logger ($message) {
		$file = '/var/log/ejabberd/external_auth.log';
		$log = @file_get_contents ($file);
		$log .= date ('d-m-Y H:i:s') . "\n=============\n" . $message . "\n\n";
		@file_put_contents ($file, $log);
		}
	}
?>
