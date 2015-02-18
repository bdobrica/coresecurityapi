<?php
class WP_CRM_Course extends WP_CRM_Model {
	const Path	= 'courses';

	public static $T = 'courses';
	protected static $K = array (
		'hash',
		'series',
		'number',
		'path',
		'name',
		'description',
		'units'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		'hash'
		);
	public static $F = array (
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
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`hash` varchar(32) NOT NULL DEFAULT \'\' UNIQUE',
		'`series` varchar(6) NOT NULL DEFAULT \'\'',
		'`number` int NOT NULL DEFAULT 0',
		'`path` text NOT NULL',
		'`name` text NOT NULL',
		'`description` text NOT NULL',
		'`units` int NOT NULL DEFAULT 0'
		);

	private $course;
	private $unit;

	public function __construct ($data = null) {
		$this->course = null;
		$this->unit = array ();

		parent::__construct ($data);

		if (file_exists (WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR . $this->data['path'])) {
			try {
				$this->course = new SQLite3 (WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR . $this->data['path'], SQLITE3_OPEN_READONLY);
				}
			catch (Exception $e) {
				$this->course = null;
				}

			if ($this->course instanceof SQLite3) {
				$unit = $this->course->querySingle ('select id from units where parent=0 and completed=0 order by oid asc limit 0,1;');
				while (!is_null ($unit)) {
					$this->unit[] = $unit;
					$unit = $this->course->querySingle (sprintf ('select id from units where parent=%d and completed=0 order by oid asc limit 0,1;', $unit));
					}
				}
			}
		}

	public static function scan () {
		if (!is_dir (WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path)) throw new WP_CRM_Exception (WP_CRM_Exception::FileSystem_Access_Error);
		if (!($d = opendir (WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path))) throw new WP_CRM_Exception (WP_CRM_Exception::FileSystem_Access_Error);

		$courses = array ();

		while (FALSE !== ($f = readdir ($d))) {
			if (in_array ($f, array ('.', '..'))) continue;
			if (!is_dir (WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR . $f)) continue;
			if (!($c = opendir (WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR . $f))) continue;

			$db_file = '';
			$db = null;

			while (FALSE !== ($i = readdir ($c))) {
				if (!preg_match ('/.[dD][bB]3?$/', $i)) continue;
				try {
					$db_file = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR . $f . DIRECTORY_SEPARATOR . $i;
					$db = new SQLite3 ($db_file, SQLITE3_OPEN_READONLY);
					}
				catch (Exception $e) {
					$db_file = '';
					var_dump ($e);
					}
				}

			if (!is_null ($db) && !empty($db_file)) {
				$db_path = str_replace (WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR, '', $db_file);
				$courses[] = array (
					'path' => $db_path,
					'hash' => md5 ($db_path),
					'data' => $db
					);
				}

			closedir ($c);
			}

		closedir ($d);

		if (!empty ($courses))
		foreach ($courses as $course) {
			$meta_rows = $course['data']->query ('select meta_key, meta_value from meta;');
			$meta_data = array ();
			while ($meta_row = $meta_rows->fetchArray (SQLITE3_ASSOC))
				$meta_data[$meta_row['meta_key']] = $meta_row['meta_value'];

			$meta_data['units'] = (int) $course['data']->querySingle ('select count(1) from units where parent=0;');
			$course['data']->close ();
			unset ($course['data']);

			$wp_crm_course = new WP_CRM_Course (array (
				'hash' => $course['hash'],
				'path' => $course['path'],
				'series' => $meta_data['series'],
				'number' => $meta_data['number'],
				'name' => $meta_data['name'],
				'description' => $meta_data['description'],
				'units' => $meta_data['units']
				));
			$wp_crm_course->save ();
			}
		}

	public function render ($class = '') {
		end($this->unit);
		$unit = $this->course->querySingle (sprintf ('select * from units where id=%d', current($this->unit)), true);
		$resources = array ();
		$res_rows = $this->course->query (sprintf ('select * from resources where unit_id=%d', current($this->unit)));
		while ($res_row = $res_rows->fetchArray (SQLITE3_ASSOC)) {
			$resources[] = $res_row;
			}

		$out = array ();
		foreach ($resources as $resource) {
			if (!isset ($out[(int) $resource['type']]))
				$out[(int) $resource['type']] = '';

			$type = (int) $resource['type'];

			switch ($type) {
				case 1: #video
					$cue_file = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR . dirname($this->data['path']) . DIRECTORY_SEPARATOR . substr ($resource['path'], 0, -3) . 'cue';
					$cue_data = array ();
					if (file_exists ($cue_file)) {
						$f = fopen ($cue_file, 'r');
						while (!feof ($f)) {
							list ($cue_point, $cue_slide) = explode (':', trim(fgets ($f, 128)));
							if (!$cue_point) continue;
							$cue_data[(int) $cue_point] = $cue_slide;
							}
						fclose ($f);
						}
					$out[$type] .= '<div class="' . $class . '-course-player" data-swf="' . get_stylesheet_directory_uri () . '/script/flowplayer/flowplayer.swf" data-ratio="0.4167"' . (!empty($cue_data) ? ('data-cuepoints="[' . implode (',', array_keys($cue_data)) . ']" data-cueslides="' . implode (',', array_values($cue_data)) . '"') : '') . '>';
					$out[$type] .= '<video preload="none">';
					$out[$type] .= '<source type="video/mp4" src="' . WP_CONTENT_URL . '/' . self::Path . '/' . dirname($this->data['path']) . '/' . $resource['path'] . '" />';
					$out[$type] .= '</video>';
					$out[$type] .= '</div>';
					break;
				case 2: #slide
					if (!is_dir (WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR . dirname($this->data['path']) . DIRECTORY_SEPARATOR . $resource['path'])) break;
					if (!($s = opendir (WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR . dirname($this->data['path']) . DIRECTORY_SEPARATOR . $resource['path']))) break;
					$out[$type] .= '<div class="' . $class . '-course-slideshow">';

					$slides = array ();
					while (FALSE !== ($i = readdir ($s))) {
						if (in_array ($i, array ('.', '..'))) continue;
						$slides[] = $i;
						}
					sort ($slides);
					foreach ($slides as $slide) {
						$out[$type] .= '<div class="' . $class . '-course-slideshow-item" data-slide="' . substr ($slide, 0, -4) . '">';
						$out[$type] .= '<img src="' . WP_CONTENT_URL . '/' . self::Path . '/' . dirname($this->data['path']) . '/' . $resource['path'] . '/' . $slide . '" />';
						$out[$type] .= '</div>';
						}

					$out[$type] .= '</div>';
					closedir ($s);
					break;
				case 3: #quiz
					/* SCHEMA:
					CREATE TABLE answers (
						id int not null primary key,
						question_id int not null default 0,
						answer text not null default '',
						type int not null default 0);

					CREATE TABLE meta (
						meta_key text not null,
						meta_value text not null);


					CREATE TABLE questions (
						id int not null primary key,
						question text not null default '',
						type int not null default 0);
					*/
					$quiz_file = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . self::Path . DIRECTORY_SEPARATOR . dirname($this->data['path']) . DIRECTORY_SEPARATOR . $resource['path'];
					if (!file_exists ($quiz_file)) break;
					try {
						$quiz_data = new SQLite3 ($quiz_file, SQLITE3_OPEN_READONLY);
						}
					catch (Exception $e) {
						break;
						}
					$meta_rows = $quiz_data->query ('select meta_key, meta_value from meta;');
					$meta_data = array ();
					while ($meta_row = $meta_rows->fetchArray (SQLITE3_ASSOC))
						$meta_data[$meta_row['meta_key']] = $meta_row['meta_value'];

					$out[$type] = '';

					$quiz_questions = array ();
					$question_rows = $quiz_data->query ('select * from questions order by id;');
					while ($question_row = $question_rows->fetchArray (SQLITE3_ASSOC))
						$quiz_questions[] = $question_row;
					
					$out[$type] .= '<div class="' . $class . '-course-quiz-view">' . "\n" . '<ul>' . "\n";
					$quiz_count = 1;
					foreach ($quiz_questions as $quiz_question) {
						$out[$type] .= '<li class="' . $class . '-course-quiz-question"><span>' . ($quiz_count++) . '.</span> ' . $quiz_question['question'] . "\n" . '<ul>' . "\n";

						$answer_rows = $quiz_data->query (sprintf ('select * from answers where question_id=%d order by id;', (int) $quiz_question['id']));
						while ($answer_row = $answer_rows->fetchArray (SQLITE3_ASSOC))
							switch ((int) $quiz_question['type']) {
								case 0:
									$out[$type] .= '<li><input type="radio" data-type="' . $answer_row['type'] . '" name="question-' . $quiz_question['id'] . '" id="answer-' . $answer_row['id'] . '" /> <label for="answer-' . $answer_row['id'] . '">' . $answer_row['answer'] . '</label></li>' . "\n";
									break;
								case 1:
									$out[$type] .= '<li><input type="checkbox" data-type="' . $answer_row['type'] . '" name="question-' . $quiz_question['id'] . '-' . $answer_row['id'] . '" id="answer-' . $answer_row['id'] . '" /> <label for="answer-' . $answer_row['id'] . '">' . $answer_row['answer'] . '</label></li>' . "\n";
									break;
								}

						$out[$type] .= '</ul>' . "\n" . '</li>' . "\n";
						}
					$out[$type] .= '</ul>' . "\n" . '</div>';
					break;
				}
			}

		return
			'<div class="row">' . 
			(!empty ($out[1]) ? (
			'<div class="' . $class . '-course-video col-md-6 col-lg-6">' . "\n" . 
				$out[1] . "\n" .
			'</div>'
				) : '') .
			(!empty ($out[2]) ? (
			'<div class="' . $class . '-course-slide col-md-6 col-lg-6">' . "\n" . 
				$out[2] . "\n" .
			'</div>'
				) : '') . 
			(!empty ($out[3]) ? (
			'<div class="' . $class . '-course-quiz col-md-6 col-lg-6">' . "\n" . 
				$out[3] . "\n" .
			'</div>'
				) : '') . 
			'</div>';
		}
	}
?>
