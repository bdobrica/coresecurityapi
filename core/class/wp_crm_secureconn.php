<?php
/**
 * Core of WP_CRM_Secure*
 */

/**
 * Wrapper for SRP. Handles encrypted connections.
 *
 * @category
 * @package WP_CRM_Secure
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_SecureConn {
	const Cache = '';

	const Chunk_Head = 8;			# holds the length of the chunk
	const Chunk_Body = 1048568;		# 8 bytes less than 1Mb

	private $sid;
	private $user;

	public function __construct ($login) {
		if (session_id()) {
			if ($_POST['session'] && (session_id() != $_POST['session'])) {
				session_unset ();
				session_destroy ();
				session_id ($_POST['session']);
				session_start ();
				}
			}
		else {
			if ($_POST['session']) session_id ($_POST['session']);
			session_start ();
			}
		
		$this->sid = session_id ();
		if (!$this->sid) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_Session);
		try {
			$this->user = new WP_CRM_User ($login);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_Username);
			}
		}

	private function _cache () {
		/* cache function for file list */
		}

	/* the cmd encrypted data is a json, containing an action:
	a	= init | remote | upload | download | remove | create
		* init		= secret server variables
				input:
					time	= client time
				output:
					time	= server time
					toff	= server time - client time (evaluating connection speed)
		* remote	= provide the client with the server filelist
				input:
					none
				output:
					[list]	= filelist
		* upload	= uploading chunks to server
				input:
					f	= (path, type, size, time, hash)
					d	= chunk data
				output:
					error	= 0|1
		* download	= downloading chunks from server
				input:
					f	= (path, type, size, time, hash)
					h	= chunk header
				output:
					d	= chunk data
		* remove	= remove server leaf
				input:
					r	= array of files and folders to be removed
				output:
					error	= 0|1
		* create	= create server leaf
				input:
					c	= array of folders to be created
				output:
					error	= 0|1
	array of files contains tuples (arrays in php)
		(path, type, size, time, hash)
		* path		= local path (relative to SecureBox folder)
		* type		= 0 for files, 1 for folders
		* size	 	= file size
		* time		= file last modified
		* hash		= file sha1(?) hash
	*/
	private function _cmd ($data) {
		switch ((string) $data['a']) {
			case 'init':
				$time = time ();
				/* get a set of initial parameters, like server time */
				return json_encode (array (
					'time' => time (),
					'toff' => $time - $data['t']
					));
				break;
			case 'remote':
				/* get the remote list of files, as a json-encoded array. */
				$list = new WP_CRM_List ('WP_CRM_SecureData', array (
					'oid=' . $this->user->ID,
					'`type`<' . WP_CRM_SecureData::Chunk
					));
				$out = array ();
				foreach ($list->get () as $file) {
					$out[] = array (
						$file->get ('path'),
						$file->get ('type'),
						$file->get ('size'),
						$file->get ('stamp'),
						$file->get ('hash')
						);
					}
				return json_encode ($out);
				break;
			case 'upload':
				/* we have a chunk that needs uploading */
				$bytes = base64_decode ($data['d']);
				$len = unpack ('l*', substr ($bytes, 0, 4));
				$body = substr ($bytes, 8);

				$len = unpack ('l*', $head);
				
				$f = fopen (self::Storage . '/' . $data['h'] . '.' . $data['c'], 'w');
				$wrt = fwrite ($f, $bytes, 
				break;
			case 'download':
				/* we have a chunk that needs to be downloaded */
				break;
			case 'remove':
				/* the files and folders in the list don't exist anymore */
				break;
			case 'create':
				/* the folders in the list should be created */
				break;
			default:
				throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_SRP_Command);
			}

		return '{"error":0}';
		}

	/* protocol:
	use https connection with $_POST as wrapper:
	u = username (mandatory, creates the WP_CRM_User object to handle SRP)
	a = challenge | check | push | pull
		* challenge	= initialize the SRP protocol
				input:
					A = initialization seed
				output:
					B
					s
		* check 	= checks client SRP "M"
				input:
					M = client computed "M"
				output:
					error: 0|1
		* push		= sends data to server without waiting for an answer
				input:
					d = encrypted data (blowfish, using SRP key)
				output:
					error: 0|1
		* pull		= sends data to server, waiting for an answer
				input:
					d = encrypted data (blowfish, using SRP key)
				output:
					error: 0|1
					d = encrypted data (blowfish, using SRP key)
	*/

	public function cmd ($data) {
		switch ((string) $data['a']) {
			case 'register':
				/* FOR TESTING ONLY! */
				if (!isset($data['v']) || (strpos($data['v'], '$') === FALSE) || (strlen($data['v']) < 20))
					throw new WP_CRM_Exception (WP_CRM_Exception::Missing_SRP_Verifier);
				break;
			case 'challenge':
				return $user->srp ('init', array ('A' => $data['A']));

			case 'check':
				return $user->srp ('server_check', array ('M' => $data['M']));

			case 'push':
				$this->_cmd (rtrim ($user->srp ('decrypt', $data['d']), "\x00"));
				break;

			case 'pull':
				$decd = json_decode (rtrim ($user->srp ('decrypt', $data['d']), "\x00"));

				if ($decd === NULL)
					throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_SRP_Command);

				$decd = (array) $decd;
				$blob = $this->_cmd ($decd);

				return json_encode (array ('error' => 0, 'd' => $blob ? $user->srp ('encrypt', $blob) : ''));

			default:
				throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_SRP_Command);
			}

		return '{"error":0}';
		}
	}
?>
