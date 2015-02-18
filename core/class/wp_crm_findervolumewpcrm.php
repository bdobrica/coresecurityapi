<?php

/**
 * Simple WP_CRM filesystem driver for MySQL.
 * The paths are of the form 1/2/3/...
 *
 * @author Dmitry (dio) Levashov
 * @author Bogdan Dobrica
 **/
class WP_CRM_FinderVolumeWPCRM extends WP_CRM_FinderVolumeDriver {
	
	/**
	 * Driver id
	 * Must be started from letter and contains [a-z0-9]
	 * Used as part of volume id
	 *
	 * @var string
	 **/
	protected $driverId = 'w';
	
	/**
	 * Directory for tmp files
	 * If not set driver will try to use tmbDir as tmpDir
	 *
	 * @var string
	 **/
	protected $tmpPath = '';
	
	/**
	 * Constructor
	 * Extend options with required fields
	 *
	 * @return void
	 * @author Bogdan Dobrica
	 **/
	public function __construct() {
		$opts = array(
			'path'		=> 1,
			'startPath'	=> 1,
			'tmbPath'       => '',
			'tmpPath'       => ''
		);
		$this->options = array_merge($this->options, $opts);
		$this->options['mimeDetect'] = 'internal';
	}
	
	/*********************************************************************/
	/*                        INIT AND CONFIGURE                         */
	/*********************************************************************/
	
	/**
	 * Prepare driver before mount volume.
	 * Connect to db, check required tables and fetch root path
	 *
	 * @return bool
	 * @author Bogdan Dobrica
	 **/
	protected function init() {
		$this->updateCache($this->options['path'], $this->_stat($this->options['path']));
		return true;
		}



	/**
	 * Set tmp path
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function configure() {
		parent::configure();

		if (($tmp = $this->options['tmpPath'])) {
			if (!file_exists($tmp)) {
				if (@mkdir($tmp)) {
					@chmod($tmp, $this->options['tmbPathMode']);
				}
			}
			
			$this->tmpPath = is_dir($tmp) && is_writable($tmp) ? $tmp : false;
		}
		
		if (!$this->tmpPath && $this->tmbPath && $this->tmbPathWritable) {
			$this->tmpPath = $this->tmbPath;
		}

		$this->mimeDetect = 'internal';
	}
	
	/**
	 * Close connection
	 *
	 * @return void
	 * @author Bogdan Dobrica
	 **/
	public function umount() {
	}
	
	/**
	 * Return debug info for client
	 *
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function debug() {
		$debug = parent::debug();
		return $debug;
	}

	/**
	 * Create empty object with required mimetype
	 *
	 * @param  string  $path  parent dir path
	 * @param  string  $name  object name
	 * @param  string  $mime  mime type
	 * @return bool
	 * @author Bogdan Dobrica
	 **/
	protected function make($path, $name, $mime) {
		$time = time ();
		try {
			$object = new WP_CRM_Folder (array (
				'parent' => $path,
				'title' => $name,
				'mime' => $mime,
				'stamp' => $time,
				'updated' => $time
				));
			$object->save ();
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			return FALSE;
			}
		return TRUE;
	}

	/**
	 * Return temporary file path for required file
	 *
	 * @param  string  $path   file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function tmpname($path) {
		return $this->tmpPath.DIRECTORY_SEPARATOR.md5($path);
	}

	/**
	 * Resize image
	 *
	 * @param  string   $hash    image file
	 * @param  int      $width   new width
	 * @param  int      $height  new height
	 * @param  bool     $crop    crop image
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
	public function resize($hash, $width, $height, $x, $y, $mode = 'resize', $bg = '', $degree = 0) {
		if ($this->commandDisabled('resize')) {
			return $this->setError(WP_CRM_Finder::ERROR_PERM_DENIED);
		}
		
		if (($file = $this->file($hash)) == false) {
			return $this->setError(WP_CRM_Finder::ERROR_FILE_NOT_FOUND);
		}
		
		if (!$file['write'] || !$file['read']) {
			return $this->setError(WP_CRM_Finder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($hash);
		
		if (!$this->canResize($path, $file)) {
			return $this->setError(WP_CRM_Finder::ERROR_UNSUPPORT_TYPE);
		}

		$img = $this->tmpname($path);
		
		if (!($fp = @fopen($img, 'w+'))) {
			return false;
		}

		if (($res = $this->query('SELECT content FROM '.$this->tbf.' WHERE id="'.$path.'"'))
		&& ($r = $res->fetch_assoc())) {
			fwrite($fp, $r['content']);
			rewind($fp);
			fclose($fp);
		} else {
			return false;
		}


		switch($mode) {
			
			case 'propresize':
				$result = $this->imgResize($img, $width, $height, true, true);
				break;

			case 'crop':
				$result = $this->imgCrop($img, $width, $height, $x, $y);
				break;

			case 'fitsquare':
				$result = $this->imgSquareFit($img, $width, $height, 'center', 'middle', $bg ? $bg : $this->options['tmbBgColor']);
				break;
			
			default:
				$result = $this->imgResize($img, $width, $height, false, true);
				break;				
    	}
		
		if ($result) {
			
			$sql = sprintf('UPDATE %s SET content=LOAD_FILE("%s"), mtime=UNIX_TIMESTAMP() WHERE id=%d', $this->tbf, $this->loadFilePath($img), $path);
			
			if (!$this->query($sql)) {
				$content = file_get_contents($img);
				$sql = sprintf('UPDATE %s SET content="%s", mtime=UNIX_TIMESTAMP() WHERE id=%d', $this->tbf, $this->db->real_escape_string($content), $path);
				if (!$this->query($sql)) {
					@unlink($img);
					return false;
				}
			}
			@unlink($img);
			if (!empty($file['tmb']) && $file['tmb'] != "1") {
				$this->rmTmb($file['tmb']);
			}
			$this->clearcache();
			return $this->stat($path);
		}
		
   		return false;
	}
	

	/*********************************************************************/
	/*                               FS API                              */
	/*********************************************************************/
	
	/**
	 * Cache dir contents
	 *
	 * @param  string  $path  dir path
	 * @return void
	 * @author Dmitry Levashov
	 * @author Bogdan Dobrica
	 **/
	protected function cacheDir($path) {
		$this->dirsCache[$path] = array();

		$objects = new WP_CRM_List ('WP_CRM_Folder', array (sprintf ('parent=%d', (int) $path)));

		if (!$objects->is ('empty'))
		foreach ($objects->get() as $object) {
			$object_path = $object->get ();
			$this->stat ($object_path);
			$this->dirsCache[$path][] = $object_path;
			}

		return $this->dirsCache[$path];
		}

	/**
	 * Return array of parents paths (ids)
	 *
	 * @param  int   $path  file path (id)
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function getParents($path) {
		$parents = array();

		while ($path) {
			if ($file = $this->stat($path)) {
				array_unshift($parents, $path);
				$path = isset($file['phash']) ? $this->decode($file['phash']) : false;
			}
		}
		
		if (count($parents)) {
			array_pop($parents);
		}
		return $parents;
	}

	/**
	 * Return correct file path for LOAD_FILE method
	 *
	 * @param  string $path  file path (id)
	 * @return string
	 * @author Troex Nevelin
	 **/
	protected function loadFilePath($path) {
		$realPath = realpath($path);
		if (DIRECTORY_SEPARATOR == '\\') { // windows
			$realPath = str_replace('\\', '\\\\', $realPath);
		}
		return $this->db->real_escape_string($realPath);
	}

	/*********************** paths/urls *************************/
	
	/**
	 * Return parent directory path
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _dirname($path) {
		return ($stat = $this->stat($path)) ? ($stat['phash'] ? $this->decode($stat['phash']) : $this->root) : false;
	}

	/**
	 * Return file name
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _basename($path) {
		return ($stat = $this->stat($path)) ? $stat['name'] : false;
	}

	/**
	 * Join dir name and file name and return full path
	 *
	 * @param  string  $dir
	 * @param  string  $name
	 * @return string
	 * @author Dmitry (dio) Levashov
	 * @author Bogdan Dobrica
	 **/
	protected function _joinPath($dir, $name) {
		$list = new WP_CRM_List ('WP_CRM_Folder', array (
			sprintf ('title="%s"', $name),
			sprintf ('parent=%d', (int) $dir)
			));
		if ($list->is ('empty')) return -1;
		return $list->get ('first')->get ();
		}
	
	/**
	 * Return normalized path, this works the same as os.path.normpath() in Python
	 *
	 * @param  string  $path  path
	 * @return string
	 * @author Troex Nevelin
	 **/
	protected function _normpath($path) {
		return $path;
	}
	
	/**
	 * Return file path related to root dir
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _relpath($path) {
		return $path;
	}
	
	/**
	 * Convert path related to root dir into real path
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _abspath($path) {
		return $path;
	}
	
	/**
	 * Return fake path started from root dir
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _path($path) {
		if (($file = $this->stat($path)) == false) {
			return '';
		}
		
		$parentsIds = $this->getParents($path);
		$path = '';
		foreach ($parentsIds as $id) {
			$dir = $this->stat($id);
			$path .= $dir['name'].$this->separator;
		}
		return $path.$file['name'];
	}
	
	/**
	 * Return true if $path is children of $parent
	 *
	 * @param  string  $path    path to check
	 * @param  string  $parent  parent path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _inpath($path, $parent) {
		return $path == $parent
			? true
			: in_array($parent, $this->getParents($path));
	}
	
	/***************** file stat ********************/
	/**
	 * Return stat for given path.
	 * Stat contains following fields:
	 * - (int)    size    file size in b. required
	 * - (int)    ts      file modification time in unix time. required
	 * - (string) mime    mimetype. required for folders, others - optionally
	 * - (bool)   read    read permissions. required
	 * - (bool)   write   write permissions. required
	 * - (bool)   locked  is object locked. optionally
	 * - (bool)   hidden  is object hidden. optionally
	 * - (string) alias   for symlinks - link target path relative to root path. optionally
	 * - (string) target  for symlinks - link target path. optionally
	 *
	 * If file does not exists - returns empty array or false.
	 *
	 * @param  string  $path    file path 
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 * @author Bogdan Dobrica
	 **/
	protected function _stat($path) {
		try {
			$object = new WP_CRM_Folder ((int) $path);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			return array ();
			}

		return array (
			'phash'		=> $this->encode($object->get ('parent')),
			'name'		=> $object->get ('title'),
			'size'		=> (int) $object->get ('size'),
			'ts'		=> (int) $object->get ('updated'),
			'mime'		=> $object->get ('mime'),
			'read'		=> TRUE,
			'write'		=> TRUE,
			'dirs'		=> !$object->get ('children')->is ('empty'),
			'file_path'	=> $object->get ('file_path')
			/**
			 * Optional:
			 */
			/*
			'locked'	=> FALSE,
			'hidden'	=> FALSE,
			'alias'		=> '',
			'target'	=> ''
			*/
			);
		}
	
	/**
	 * Return true if path is dir and has at least one childs directory
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _subdirs($path) {
		return ($stat = $this->stat($path)) && isset($stat['dirs']) ? $stat['dirs'] : false;
	}
	
	/**
	 * Return object width and height
	 * Usualy used for images, but can be realize for video etc...
	 *
	 * @param  string  $path  file path
	 * @param  string  $mime  file mime type
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _dimensions($path, $mime) {
		return ($stat = $this->stat($path)) && isset($stat['width']) && isset($stat['height']) ? $stat['width'].'x'.$stat['height'] : '';
	}
	
	/******************** file/dir content *********************/
		
	/**
	 * Return files list in directory.
	 *
	 * @param  string  $path  dir path
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _scandir($path) {
		return isset($this->dirsCache[$path])
			? $this->dirsCache[$path]
			: $this->cacheDir($path);
	}
		
	/**
	 * Open file and return file pointer
	 *
	 * @param  string  $path  file path
	 * @param  string  $mode  open file mode (ignored in this driver)
	 * @return resource|false
	 * @author Dmitry (dio) Levashov
	 * @author Bogdan Dobrica
	 **/
	protected function _fopen($path, $mode='rb') {
		$stat = $this->stat($path);
		if (isset($stat['file_path']) && file_exists($stat['file_path'])) return fopen ($stat['file_path'], $mode);
		return FALSE;
		}
	
	/**
	 * Close opened file
	 *
	 * @param  resource  $fp  file pointer
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 * @author Bogdan Dobrica
	 **/
	protected function _fclose($fp, $path='') {
		@fclose($fp);
		}
	
	/********************  file/dir manipulations *************************/
	
	/**
	 * Create dir and return created dir path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new directory name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 * @author Bogdan Dobrica
	 **/
	protected function _mkdir($path, $name) {
		return $this->make($path, $name, 'directory') ? $this->_joinPath($path, $name) : false;
		}
	
	/**
	 * Create file and return it's path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new file name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 * @author Bogdan DObrica
	 **/
	protected function _mkfile($path, $name) {
		return $this->make($path, $name, 'text/plain') ? $this->_joinPath($path, $name) : false;
		}
	
	/**
	 * Create symlink. FTP driver does not support symlinks.
	 *
	 * @param  string  $target  link target
	 * @param  string  $path    symlink path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _symlink($target, $path, $name) {
		return false;
		}
	
	/**
	 * Copy file into another file
	 *
	 * @param  string  $source     source file path
	 * @param  string  $targetDir  target directory path
	 * @param  string  $name       new file name
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _copy($source, $targetDir, $name) {
		$this->clearcache();
		$id = $this->_joinPath($targetDir, $name);

		$sql = $id > 0
			? sprintf('REPLACE INTO %s (id, parent_id, name, content, size, mtime, mime, width, height, `read`, `write`, `locked`, `hidden`) (SELECT %d, %d, name, content, size, mtime, mime, width, height, `read`, `write`, `locked`, `hidden` FROM %s WHERE id=%d)', $this->tbf, $id, $this->_dirname($id), $this->tbf, $source)
			: sprintf('INSERT INTO %s (parent_id, name, content, size, mtime, mime, width, height, `read`, `write`, `locked`, `hidden`) SELECT %d, "%s", content, size, %d, mime, width, height, `read`, `write`, `locked`, `hidden` FROM %s WHERE id=%d', $this->tbf, $targetDir, $this->db->real_escape_string($name), time(), $this->tbf, $source);

		return $this->query($sql);
	}
	
	/**
	 * Move file into another parent dir.
	 * Return new file path or false.
	 *
	 * @param  string  $source  source file path
	 * @param  string  $target  target dir path
	 * @param  string  $name    file name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _move($source, $targetDir, $name) {
		$sql = 'UPDATE %s SET parent_id=%d, name="%s" WHERE id=%d LIMIT 1';
		$sql = sprintf($sql, $this->tbf, $targetDir, $this->db->real_escape_string($name), $source);
		return $this->query($sql) && $this->db->affected_rows > 0;
	}
		
	/**
	 * Remove file
	 *
	 * @param  string  $path  file path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _unlink($path) {
		return $this->query(sprintf('DELETE FROM %s WHERE id=%d AND mime!="directory" LIMIT 1', $this->tbf, $path)) && $this->db->affected_rows;
	}

	/**
	 * Remove dir
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _rmdir($path) {
		return $this->query(sprintf('DELETE FROM %s WHERE id=%d AND mime="directory" LIMIT 1', $this->tbf, $path)) && $this->db->affected_rows;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Dmitry Levashov
	 **/
	protected function _setContent($path, $fp) {
		rewind($fp);
		$fstat = fstat($fp);
		$size = $fstat['size'];
		
		
	}
	
	/**
	 * Create new file and write into it from file pointer.
	 * Return new file path or false on error.
	 *
	 * @param  resource  $fp   file pointer
	 * @param  string    $dir  target dir path
	 * @param  string    $name file name
	 * @return bool|string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _save($fp, $dir, $name, $mime, $w, $h) {
		$this->clearcache();
		
		$id = $this->_joinPath($dir, $name);
		rewind($fp);
		$stat = fstat($fp);
		$size = $stat['size'];
		
		if (($tmpfile = tempnam($this->tmpPath, $this->id))) {
			if (($trgfp = fopen($tmpfile, 'wb')) == false) {
				unlink($tmpfile);
			} else {
				while (!feof($fp)) {
					fwrite($trgfp, fread($fp, 8192));
				}
				fclose($trgfp);
				
				$sql = $id > 0
					? 'REPLACE INTO %s (id, parent_id, name, content, size, mtime, mime, width, height) VALUES ('.$id.', %d, "%s", LOAD_FILE("%s"), %d, %d, "%s", %d, %d)'
					: 'INSERT INTO %s (parent_id, name, content, size, mtime, mime, width, height) VALUES (%d, "%s", LOAD_FILE("%s"), %d, %d, "%s", %d, %d)';
				$sql = sprintf($sql, $this->tbf, $dir, $this->db->real_escape_string($name), $this->loadFilePath($tmpfile), $size, time(), $mime, $w, $h);

				$res = $this->query($sql);
				unlink($tmpfile);
				
				if ($res) {
					return $id > 0 ? $id : $this->db->insert_id;
				}
			}
		}

		
		$content = '';
		rewind($fp);
		while (!feof($fp)) {
			$content .= fread($fp, 8192);
		}
		
		$sql = $id > 0
			? 'REPLACE INTO %s (id, parent_id, name, content, size, mtime, mime, width, height) VALUES ('.$id.', %d, "%s", "%s", %d, %d, "%s", %d, %d)'
			: 'INSERT INTO %s (parent_id, name, content, size, mtime, mime, width, height) VALUES (%d, "%s", "%s", %d, %d, "%s", %d, %d)';
		$sql = sprintf($sql, $this->tbf, $dir, $this->db->real_escape_string($name), $this->db->real_escape_string($content), $size, time(), $mime, $w, $h);
		
		unset($content);

		if ($this->query($sql)) {
			return $id > 0 ? $id : $this->db->insert_id;
		}
		
		return false;
	}
	
	/**
	 * Get file contents
	 *
	 * @param  string  $path  file path
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _getContents($path) {
		return ($res = $this->query(sprintf('SELECT content FROM %s WHERE id=%d', $this->tbf, $path))) && ($r = $res->fetch_assoc()) ? $r['content'] : false;
	}
	
	/**
	 * Write a string to a file
	 *
	 * @param  string  $path     file path
	 * @param  string  $content  new file content
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _filePutContents($path, $content) {
		return $this->query(sprintf('UPDATE %s SET content="%s", size=%d, mtime=%d WHERE id=%d LIMIT 1', $this->tbf, $this->db->real_escape_string($content), strlen($content), time(), $path));
	}

	/**
	 * Detect available archivers
	 *
	 * @return void
	 **/
	protected function _checkArchivers() {
		return;
	}

	/**
	 * Unpack archive
	 *
	 * @param  string  $path  archive path
	 * @param  array   $arc   archiver command and arguments (same as in $this->archivers)
	 * @return void
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
	protected function _unpack($path, $arc) {
		return;
	}

	/**
	 * Recursive symlinks search
	 *
	 * @param  string  $path  file/dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _findSymlinks($path) {
		return false;
	}

	/**
	 * Extract files from archive
	 *
	 * @param  string  $path  archive path
	 * @param  array   $arc   archiver command and arguments (same as in $this->archivers)
	 * @return true
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	protected function _extract($path, $arc) {
		return false;
	}
	
	/**
	 * Create archive and return its path
	 *
	 * @param  string  $dir    target dir
	 * @param  array   $files  files names list
	 * @param  string  $name   archive name
	 * @param  array   $arc    archiver options
	 * @return string|bool
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	protected function _archive($dir, $files, $name, $arc) {
		return false;
	}
	
} // END class 
