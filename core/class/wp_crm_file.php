<?php
/**
 * Core of WP_CRM_*
 */

/**
 * File is an object useful for archiving. An archive should have storage spaces => Folders mainly
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
define ('WP_CRM_FILE_URL', WP_CONTENT_URL . DIRECTORY_SEPARATOR . 'wp-crm');
define ('WP_CRM_FILE_PATH', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'wp-crm');

/**
 * File FS structure:
 * -> filename.ext (file, current version)
 * -> ./.filename.ext.dat (folder)
 * -> ./.filename.ext.dat/v/
 * -> ./.filename.ext.dat/v/v[version_number]
 * -> ./.filename.ext.dat/i/
 * -> ./.filename.ext.dat/i/ocr.txt
 * -> ./.filename.ext.dat/i/ocr.docx
 * -> ./.filename.ext.dat/s/s[dpi_3digit]_[scan_options]
*/

class WP_CRM_File extends WP_CRM_Model {
	const URL	= WP_CRM_FILE_URL;
	const Path	= WP_CRM_FILE_PATH;
	const Salt	= 'Pf8G9cQnbHebRqpM';

	public static $T = 'files';
	protected static $K = array (
		'oid',				/** the office id */
		'cid',				/** the company id */
		'uid',				/** the user (owner) id */
		'path',				/** the path for this file. usually the /path/to/hash.type */
		'title',			/** the title (name) of the file */
		'description',			/** short description of the file */
		'hash',				/** the 128-bit hash for this file. thinking of using mmh3 128-bit version */
		'type',				/** the extension of the file (8 chars) */
		'length',			/** the length of this file in bytes */
		'atime',			/** the last access time of this file */
		'ctime',			/** the last inode change of this file */
		'mtime',			/** the last modificiation time of this file */
		'stamp'				/** the creation timestamp */
		);
	protected static $M_K = array (
		//'content'			/** stores the OCRd content */
		);
	protected static $U = array (
		'hash',
		'type'
		);
	public static $F = array (
		'new' => array (
			'title' => 'Denumire',
			'description' => 'Descriere',
			'path:file' => 'Fisier'
			),
		'edit' => array (
			'title' => 'Denumire',
			'description' => 'Descriere',
			'path:file' => 'Fisier'
			),
		'view' => array (
			'title' => 'Denumire',
			'description' => 'Descriere',
			'path:file' => 'Fisier'
			),
		'safe' => array (
			),
		'excerpt' => array (
			),
		'group' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`path` text NOT NULL',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`hash` varchar(32) NOT NULL DEFAULT \'\'',
		'`type` varchar(8) NOT NULL DEFAULT \'\'',
		'`length` int(11) NOT NULL DEFAULT 0',
		'`atime` int(11) NOT NULL DEFAULT 0',
		'`ctime` int(11) NOT NULL DEFAULT 0',
		'`mtime` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'UNIQUE (`hash`, `type`)',
		'FULLTEXT (`title`,`description`)'
		);

	public function __construct ($data = null) {
		/*
		 * if data is an array, it should have at least:
		 * 	path = the path of the file
		 *	name = the name of the file (for uploaded files only)
		 */
		global $current_user;

		if (is_array ($data) && isset ($data['path'])) {
			if (is_uploaded_file ($data['path'])) {
				if (!$current_user->ID) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_Session);

				$data['length'] = filesize ($data['path']);
				if (!$data['length']) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_File_Size);

				$data['hash'] = self::hash ($data['path']);
				$data['type'] = self::type ($data['name']);

				$err = @move_uploaded_file ($data['path'], self::path (array (
					'hash' => $data['hash'],
					'type' => $data['type']
					)));

				if ($err === FALSE) throw new WP_CRM_Exception (WP_CRM_Exception::File_Upload_Error);

				$stamp = time ();

				$data = array (
					'uid'		=> $current_user->ID,
					'path'		=> self::path (array (
								'hash' => $data['hash'],
								'type' => $data['type']
								)),
					'title'		=> $data['name'],
					'hash'		=> $data['hash'],
					'type'		=> $data['type'],
					'length'	=> $data['length'],
					'atime'		=> $stamp,
					'ctime'		=> $stamp,
					'mtime'		=> $stamp,
					'stamp' 	=> $stamp
					);
				}
			else
			/*
			if (file_exists ($data['path'])) {
				if (dirname ($data['path']) === self::Path) {
					$file = basename ($data['path']);
					$type = self::type ($file);
					$hash = self::name ($file);

					$data['name'] = $file;
					$data['hash'] = $hash;
					$data['type'] = $type; 
					}
				}
			else
			*/
			if ($data['path'] === 'input') {
				if (!$data['length']) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_File_Size);

				$data['hash'] = self::hash ('php://input');
				$data['type'] = self::type ($data['name']);

				$err = @copy ('php://input', self::path (array (
					'hash' => $data['hash'],
					'type' => $data['type']
					)));

				if ($err === FALSE) throw new WP_CRM_Exception (WP_CRM_Exception::File_Upload_Error);

				$stamp = time ();

				$data = array (
					'uid'		=> $current_user->ID,
					'path'		=> self::path (array (
								'hash' => $data['hash'],
								'type' => $data['type']
								)),
					'title'		=> $data['name'],
					'hash'		=> $data['hash'],
					'type'		=> $data['type'],
					'length'	=> $data['length'],
					'atime'		=> $stamp,
					'ctime'		=> $stamp,
					'mtime'		=> $stamp,
					'stamp' 	=> $stamp
					);
				}
			}
		parent::__construct ($data);
		}

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'url':
					return self::url ($this->data['path']);
					break;
				}
			}
		return parent::get ($key, $opts);
		}

	public function json ($data = null) {
		return json_encode ((object) array (
			'url'	=> self::url ($this->data['path']),
			'name'	=> $this->data['title']
			));
		}

	private static function url ($path) {
		return str_replace (self::Path, self::URL, $path);
		}

	private static function path ($data = array ()) {
		return self::Path . DIRECTORY_SEPARATOR . $data['hash'] . '.' . $data['type'];
		}

	private static function name ($name) {
		return empty($name) ? FALSE : strtolower (substr ($name, 0, strrpos ($name, '.')));
		}

	private static function type ($name) {
		return empty($name) ? 'unk' : strtolower (substr ($name, 1 + strrpos ($name, '.')));
		}

	private static function hash ($file) {
		return md5_file ($file);
		}
	};
?>
