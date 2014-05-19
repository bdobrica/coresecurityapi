<?php
/**
 * Core of WP_CRM_Secure*
 */

/**
 * Class for holding leaf data structures (files & folders) in database.
 *
 * @category
 * @package WP_CRM_Secure
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_SecureLeaf extends WP_CRM_Model {
	const File		= 0;
	const Folder		= 1;
	const File_Copy		= 2;
	const Folder_Copy	= 3;

	public static $T = 'secureleafs';
	protected static $K = array (
		'oid',			# owner id
		'pid',			# parent ID
		'name',			# file name
		'path',			# file path (local, relative to SecureBox folder)
		'type',			# file type
		'size',			# size in bytes
		'hash',			# file hash (not piece hash)
		'mtime',		# modified time
		'version',		# file version
		'stamp',		# leaf creation date
		);
	protected static $F = array (
		'new' => array (
			),
		'edit' => array (
			),
		'view' => array (
			),
		'safe' => array (
			),
		'excerpt' => array (
			),
		'group' => array (
			)
		);
	protected static $Q = array (
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`name` text NOT NULL',
		'`path` text NOT NULL',
		'`type` int(11) NOT NULL DEFAULT 0',
		'`size` int(11) NOT NULL DEFAULT 0',
		'`hash` char(128) NOT NULL DEFAULT \'\'',
		'`version` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'FULL TEXT(`name`)',
		'KEY (`type`)',
		'KEY (`pid`)',
		'UNIQUE(`pid`,`hash`)',
		);

	const Chunk	= 1016;
	const First	= 8;
	const Storage	= '/mnt/www/coresecurity.ro/.storage';

	private $_fseek;

	public function chunk ($fseek = null) {
		if (!is_null ($fseek))
			$this->_fseek = $fseek;
		if ($this->_fseek > $this->data['size'])
			return null;

		$f = fopen ($this->data['path'], 'rb');
		fseek ($f, $this->_fseek);
		$c = fread ($f, self::Chunk);
		fclose ($f);

		$l = strlen ($c);
		$s = $this->_fseek;
		$this->_fseek += $l;

		return pack ('L*', 0, $l, 0, $s) . $c;
		}

	public function stick ($data) {
		$head = unpack ('L*', substr ($data, 0, 16));
		$d = substr ($data, 16);
		$l = $head[2];
		$s = $head[4];

		if ($this->data['size'] < $s) {
			$f = fopen ($this->data['path'], 'ab');
			fwrite ($f, str_pad ('', $s - $this->data['size'], "\x00"));
			fclose ($f);
			$this->data['size'] = $s;
			}

		$f = fopen ($this->data['path'], 'r+b');
		if ($s > 0)
			fseek ($f, $s);
		fwrite ($f, $d);
		fclose ($f);

		$this->data['size'] += $l;
		}
	}
?>
