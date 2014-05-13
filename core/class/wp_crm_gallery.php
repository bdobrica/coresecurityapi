<?php
class WP_CRM_Gallery {
	private $ID;
	private $title;
	private $description;
	private $images;
	private $current;

	public function __construct ($data = '') {
		global $wpdb;
		$this->images = array ();
		if (is_numeric($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'galleries` where id=%d', (int) $data);
			$gallery = $wpdb->get_row ($sql);

			$this->ID = (int) $data;
			$this->title = $gallery->title;
			$this->description = $gallery->description;

			$this->images = $wpdb->get_col ($wpdb->prepare('select id from `'.$wpdb->prefix.'gallery_files` where gid=%d order by path', (int) $this->ID));
			}
		if (isset($_SESSION['WP_CRM_GALLERY_CURRENT'])) $this->current = (int) $_SESSION['WP_CRM_GALLERY_CURRENT'];
		}

	public function get ($key = '', $value = '') {
		if ($key == 'images') return $this->images;
		if ($key == 'current image') return (is_array($this->images) && !empty($this->images)) ? new WP_CRM_Image ($this->images[$this->current]) : null;
		return $this->ID;
		}

	public function next () {
		$this->current ++;
		$this->current = (is_array($this->images) && !empty($this->images)) ? ($this->current % ((int) count($this->images))) : 0;
		$_SESSION['WP_CRM_GALLERY_CURRENT'] = $this->current;
		}
	public function prev () {
		$this->current --;
		$this->current = (is_array($this->images) && !empty($this->images)) ? (((int) (count($this->images) + $this->current)) % ((int) count($this->images))) : 0;
		$_SESSION['WP_CRM_GALLERY_CURRENT'] = $this->current;
		}

	public function reset () {
		$this->current = 0;
		$_SESSION['WP_CRM_GALLERY_CURRENT'] = $this->current;
		}

	private function _ext ($path) {
		$dot = strrpos($path, '.');
		if ($dot === FALSE) return null;
		return trim(strtolower(substr($path, $dot + 1)));
		}

	public function scan ($path) {
		global $wpdb;
		$path = rtrim($path, '/');
		if (!$this->ID) {
			$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'galleries` (hash,title,description,stamp) values (%s,%s,%s,%d);', array (
				md5($path),
				$this->title,
				$this->description,
				time()
				));
			$wpdb->query ($sql);
			$this->ID = (int) $wpdb->insert_id;
			}
		if (!is_dir($path)) return null;
		if (!($d = opendir($path))) return null;
		while (($f = readdir($d)) !== FALSE) {
			if (!in_array($this->_ext($path . '/' . $f), array ('jpg', 'jpeg', 'png'))) continue;
			$image = new WP_CRM_Image (array ('path' => $path . '/' . $f, 'gallery' => $this->ID));
			if ($image->get ())
				$this->images[] = $image->get ();
			}
		closedir ($d);
		}

	public function out ($echo = FALSE) {
		$out = '';

		if (!empty($this->images))
			foreach ($this->images as $image) {
				$image = new WP_CRM_Image ($image);
				$out .= '<div class="wp-crm-gallery-thumb" rel="' . $image->get() . '">';
				$out .= $image->out ('thumb');
				$out .= '</div>';
				}

		$out = '<div class="wp-crm-gallery-container"><div class="wp-crm-gallery-view"></div>
<div class="wp-crm-gallery-prev"></div><div class="wp-crm-gallery-next"></div>
<div class="wp-crm-gallery-social">
<div style="width: 52px; overflow: hidden; background: #eee; border: 1px solid #ddd; margin: 15px 0 5px 12px; padding: 2px 0; border-radius: 3px;"><fb:share-button id="wp-crm-gallery-fb-share" href="" show_faces="true" width="60"></fb:share-button></div>
<fb:like id="wp-crm-gallery-fb-like" href="" send="true" layout="box_count" width="100" show_faces="true"></fb:like>
<a class="wp-crm-gallery-download" href="" target="_blank" title="Download">Download</a>
</div>
<div style="clear: both;"></div>
<div class="wp-crm-gallery-thumbs">' . $out . '</div><div style="clear: both;"></div></div>';

		if ($echo) echo $out;
		return $out;
		}

	public function __destruct () {
		}
	}
?>