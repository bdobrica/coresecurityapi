<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Dummy object. Shows how to create a new object.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Wiki extends WP_CRM_Model {
	public static $T = 'wikis';
	protected static $K = array (
		'oid',
		'cid',
		'uid',
		'slug',
		'title',
		'description',
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		'slug'
		);
	public static $F = array (
		'new' => array (
			'slug' => 'Legatura',
			'title'	=> 'Denumire',
			'description' => 'Descriere'
			),
		'edit' => array (
			'slug' => 'Legatura',
			'title'	=> 'Denumire',
			'description' => 'Descriere'
			),
		'view' => array (
			'slug' => 'Legatura',
			'title'	=> 'Denumire',
			'description' => 'Descriere'
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
		'`parent` int(11) NOT NULL DEFAULT 0',
		'`slug` varchar(64) NOT NULL DEFAULT \'\'',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'UNIQUE(`slug`)',
		'FULLTEXT(`title`,`description`)'
		);

	public function render ($class = '') {
		$out = '';
		$pages = new WP_CRM_List ('WP_CRM_Wiki_Page', array (sprintf ('parent=%d', $this->ID)));
		$page = $pages->get ('first');
		$out .= '<div class="row">
	<div class="col-md-4">
		<h4>' . $this->data['title'] . '</h4>
		<ul>
			<li><a href="#">Prima Pagina</a></li>
			<li><a href="#">Pagina Aleatoare</a></li>
		</ul>
	</div>
	<div class="col-md-8">';
		$view = new WP_CRM_View ($page);
		$out .= $view->get ();
		unset ($view);
		$out .= '	</div>
</div>';
		return $out;
		}

	private static function syntax ($text, $optimize = FALSE) {
		$patterns = array (
			"/\r\n/",

			/** Headings */
			"/^==== (.+?) ====$/m",						// Subsubheading
			"/^=== (.+?) ===$/m",						// Subheading
			"/^== (.+?) ==$/m",						// Heading

			/** Formatting */
			"/\'\'\'\'\'(.+?)\'\'\'\'\'/s",					// Bold-italic
			"/\'\'\'(.+?)\'\'\'/s",						// Bold
			"/\'\'(.+?)\'\'/s",						// Italic

			/** Special */
			"/^----+(\s*)$/m",						// Horizontal line
			"/\[\[(file|img):((ht|f)tp(s?):\/\/(.+?))( (.+))*\]\]/i",	// (File|img):(http|https|ftp) aka image
			"/\[((news|(ht|f)tp(s?)|irc):\/\/(.+?))( (.+))\]/i",		// Other urls with text
			"/\[((news|(ht|f)tp(s?)|irc):\/\/(.+?))\]/i",			// Other urls without text

			// Indentations
			"/[\n\r]: *.+([\n\r]:+.+)*/",					// Indentation first pass
			"/^:(?!:) *(.+)$/m",						// Indentation second pass
			"/([\n\r]:: *.+)+/",						// Subindentation first pass
			"/^:: *(.+)$/m",						// Subindentation second pass

			// Ordered list
			"/[\n\r]?#.+([\n|\r]#.+)+/",					// First pass, finding all blocks
			"/[\n\r]#(?!#) *(.+)(([\n\r]#{2,}.+)+)/",			// List item with sub items of 2 or more
			"/[\n\r]#{2}(?!#) *(.+)(([\n\r]#{3,}.+)+)/",			// List item with sub items of 3 or more
			"/[\n\r]#{3}(?!#) *(.+)(([\n\r]#{4,}.+)+)/",			// List item with sub items of 4 or more

			// Unordered list
			"/[\n\r]?\*.+([\n|\r]\*.+)+/",					// First pass, finding all blocks
			"/[\n\r]\*(?!\*) *(.+)(([\n\r]\*{2,}.+)+)/",			// List item with sub items of 2 or more
			"/[\n\r]\*{2}(?!\*) *(.+)(([\n\r]\*{3,}.+)+)/",			// List item with sub items of 3 or more
			"/[\n\r]\*{3}(?!\*) *(.+)(([\n\r]\*{4,}.+)+)/",			// List item with sub items of 4 or more

			// List items
			"/^[#\*]+ *(.+)$/m",						// Wraps all list items to <li/>

			// Newlines (TODO: make it smarter and so that it groupd paragraphs)
			"/^(?!<li|dd).+(?=(<a|strong|em|img)).+$/mi",			// Ones with breakable elements (TODO: Fix this crap, the li|dd comparison here is just stupid)
			"/^[^><\n\r]+$/m",						// Ones with no elements
			);

		$replacements = array (
			"\n",

			// Headings
			"<h3>$1</h3>",
			"<h2>$1</h2>",
			"<h1>$1</h1>",

			//Formatting
			"<strong><em>$1</em></strong>",
			"<strong>$1</strong>",
			"<em>$1</em>",

			// Special
			"<hr/>",
			"<img src=\"$2\" alt=\"$6\"/>",
			"<a href=\"$1\">$7</a>",
			"<a href=\"$1\">$1</a>",

			// Indentations
			"\n<dl>$0\n</dl>", // Newline is here to make the second pass easier
			"<dd>$1</dd>",
			"\n<dd><dl>$0\n</dl></dd>",
			"<dd>$1</dd>",

			// Ordered list
			"\n<ol>\n$0\n</ol>",
			"\n<li>$1\n<ol>$2\n</ol>\n</li>",
			"\n<li>$1\n<ol>$2\n</ol>\n</li>",
			"\n<li>$1\n<ol>$2\n</ol>\n</li>",

			// Unordered list
			"\n<ul>\n$0\n</ul>",
			"\n<li>$1\n<ul>$2\n</ul>\n</li>",
			"\n<li>$1\n<ul>$2\n</ul>\n</li>",
			"\n<li>$1\n<ul>$2\n</ul>\n</li>",

			// List items
			"<li>$1</li>",

			// Newlines
			"$0<br/>",
			"$0<br/>",
			);

		if ($optimize) foreach ($partterns as $key => $value) $patterns[$key] .= 'S';

		return preg_replace ($patterns, $replacements, $text);
		}

	private static function diff ($a, $b) {
		}
	}
?>
