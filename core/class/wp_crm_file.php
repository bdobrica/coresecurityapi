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

	private $append;

	public function __construct ($data = null) {
		/*
		 * if data is an array, it should have at least:
		 * 	path = the path of the file
		 *	name = the name of the file (for uploaded files only)
		 */
		global
			$current_user,
			$wpdb;

		if (is_array ($data) && isset ($data['path'])) {
			$this->append = $data['append'] ? $data['append'] : '';

			if (is_uploaded_file ($data['path'])) {
				if (!$current_user->ID) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_Session);

				$data['length'] = filesize ($data['path']);
				if (!$data['length']) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_File_Size);

				$data['hash'] = self::hash ($data['path']);
				$data['type'] = self::type ($data['name']);

				$err = @move_uploaded_file ($data['path'], self::path (array (
					'append' => $this->append,
					'hash' => $data['hash'],
					'type' => $data['type']
					)));

				if ($err === FALSE) throw new WP_CRM_Exception (WP_CRM_Exception::File_Upload_Error);

				$stamp = time ();

				$data = array (
					'uid'		=> $current_user->ID,
					'path'		=> self::path (array (
								'append' => $this->append,
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
					'append' => $this->append,
					'hash' => $data['hash'],
					'type' => $data['type']
					)));

				if ($err === FALSE) throw new WP_CRM_Exception (WP_CRM_Exception::File_Upload_Error);

				$stamp = time ();

				$data = array (
					'uid'		=> $current_user->ID,
					'path'		=> self::path (array (
								'append' => $this->append,
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
		else
		if (is_string ($data) && !is_numeric ($data)) {
			$dot = strrpos ($data, '.');
			$sep = strrpos ($data, DIRECTORY_SEPARATOR);
			if (($sep === FALSE) || ($dot === FALSE) || ($sep > $dot)) throw new WP_CRM_Exception (WP_CRM_Exception::Unknown_File);
			$data = array (
				/** 'hash' =>	*/ substr ($data, $sep + 1, $dot - $sep - 1),
				/** 'type' =>	*/ substr ($data, $dot + 1)
				);

			$row = $wpdb->get_row ($wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where hash=%s and type=%s;', $data), ARRAY_A);
			if (!empty ($row)) {
				$this->ID = (int) $row['id'];
				$this->data = $row;
				return;
				}
			}
		parent::__construct ($data);
		}

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'url':
					if (is_string ($opts)) {
						switch ($this->data['type']) {
							case 'jpg':
							case 'png':
								return self::resize (self::url ($this->data['path']), $opts);
								break;
							}
						}
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

	public static function url ($path, $reverse = FALSE) {
		return $reverse ?
			str_replace (self::URL, self::Path, $path):
			str_replace (self::Path, self::URL, $path);
		}

	private static function path ($data = array ()) {
		return self::Path . DIRECTORY_SEPARATOR . ($data['append'] ? $data['append'] . DIRECTORY_SEPARATOR : '') . $data['hash'] . '.' . $data['type'];
		}

	private static function name ($name) {
		return empty($name) ? FALSE : strtolower (substr ($name, 0, strrpos ($name, '.')));
		}

	private static function type ($name) {
		return empty($name) ? 'unk' : strtolower (substr ($name, 1 + strrpos ($name, '.')));
		}

	public static function resize ($name, $size) {
		$crop = FALSE;
		if (strpos ($size, 'c') === 0) { $crop = TRUE; $size = substr ($size, 1); }

		list ($width, $height) = explode ('x', $size);
		$width = (int) $width;
		$height = (int) $height;
		if ($width * $height == 0) return FALSE;

		$is_url = FALSE;
		if (strpos ($name, self::URL) === 0) {
			$is_url = TRUE;
			$name = self::url ($name, TRUE);
			}
		if (strpos ($name, self::Path) !== 0) return FALSE;
		$path = dirname ($name);
		$file = basename ($name);
		$type = self::type ($file);
		if (!in_array ($type, array ('jpg', 'png'))) return FALSE;
		$resize_file = self::name ($file) . '-' . $size . '.' . $type;
		$resize_name = $path . DIRECTORY_SEPARATOR . $resize_file;
		if (file_exists ($resize_name)) return $is_url ? self::url ($resize_name) : $resize_name;
		
		$image = new Imagick ();
		$image->readImage ($name);

		if ($crop) {
			$s = $image->getImageGeometry ();
			$hr = $s['height'] / $height;
			$wr = $s['width'] / $width;

			if ($wr > $hr) {
				$cwidth = $hr * $width;
				$owidth = ceil (($s['width'] - $cwidth) / 2);
				$image->cropImage ($cwidth, $s['height'], $owidth, 0);
				}
			else {
				$cheight = $wr * $height;
				$oheight = ceil (($s['height'] - $cheight) / 2);
				$image->cropImage ($s['width'], $cheight, 0, $oheight);
				}
			}
		
		$image->resizeImage ($width, $height, Imagick::FILTER_CATROM, 1.0, TRUE);
		$image->writeImage ($resize_name);
		$image->clear ();
		$image->destroy ();
		
		return $is_url ? self::url ($resize_name) : $resize_name;
		}

	private static function hash ($file) {
		return md5_file ($file);
		}

	public function __toString () {
		return self::url ($this->data['path']);
		}
	};
?>
