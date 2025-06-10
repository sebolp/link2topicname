<?php
	/**
		*
		* link2topicname. An extension for the phpBB Forum Software package.
		*
		* @copyright (c) 2025, sebo, https://www.fiatpandaclub.org
		* @license GNU General Public License, version 2 (GPL-2.0)
		*
	*/

	namespace sebo\link2topicname\event;

	use Symfony\Component\EventDispatcher\EventSubscriberInterface;

	class main_listener implements EventSubscriberInterface
	{
		public static function getSubscribedEvents()
		{
			return [
			'core.user_setup'							=> 'load_language_on_setup',
			'core.viewtopic_modify_post_row'			=> 'edit_postrow',
			];
		}

		/** @var \phpbb\language\language */
		protected $language;

		/** @var \phpbb\db\driver\driver_interface */
		protected $db;

		/** @var \phpbb\template\template */
		protected $template;

		/** @var php_ext */
		protected $php_ext;

		/** @var string */
		protected $phpbb_root_path;

		/** @var table_prefix */
		protected $table_prefix;

		/** @var array */
		protected $settings = [];

		public function __construct
		(
		\phpbb\language\language $language,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\template\template $template,
		$php_ext,
		$phpbb_root_path,
		$table_prefix
		)
		{
			$this->language = $language;
			$this->db = $db;
			$this->template = $template;
			$this->php_ext = $php_ext;
			$this->phpbb_root_path = $phpbb_root_path;
			$this->table_prefix = $table_prefix;
			$this->load_settings();
		}

		protected function load_settings(): void
		{
			$sql = 'SELECT * FROM ' . $this->table_prefix . 'sebo_l2t_settings';
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row)
			{
				$this->settings = [
				'car_length'     => (int) $row['car_length'],
				'view_username'  => (bool) $row['view_username'],
				'view_avatar'    => (bool) $row['view_avatar'],
				'view_forum'     => (bool) $row['view_forum'],
				'view_popup'     => (bool) $row['view_popup'],
				'view_text'      => (bool) $row['view_text'],
				'view_bbcode'    => (bool) $row['text_formatted'],
				// for signature replace
				'enable_l2tsignature'	=> (bool) $row['enable_l2tsignature'],
				'view_username_sig'  => (bool) $row['view_username_sig'],
				'view_avatar_sig'    => (bool) $row['view_avatar_sig'],
				'view_forum_sig'     => (bool) $row['view_forum_sig'],
				'view_popup_sig'     => (bool) $row['view_popup_sig'],
				'view_text_sig'      => (bool) $row['view_text_sig'],
				'view_bbcode_sig'    => (bool) $row['text_formatted_sig'],
				];
			}

			// general settings
			$board_url = generate_board_url();

			$parsed_board_url = parse_url($board_url);
			$host = preg_quote(preg_replace('~^www\.~i', '', $parsed_board_url['host']), '~');
			$base_path = preg_quote($parsed_board_url['path'] ?? '', '~');

			$this->settings['board_url'] = $board_url;
			$this->settings['base_path'] = $base_path;
			$this->settings['pattern'] = '~<a[^>]+href="https?://(?:www\.)?' . $host . $base_path . '/(viewtopic|viewforum)\.php\?([^"#]*)(#[^"]*)?"[^>]*>(.*?)</a>~i';
		}

		public function load_language_on_setup($event)
		{
			$lang_set_ext = $event['lang_set_ext'];
			$lang_set_ext[] = [
			'ext_name' => 'sebo/link2topicname',
			'lang_set' => 'common',
			];
			$event['lang_set_ext'] = $lang_set_ext;
		}

		protected function get_user_info(int $user_id): array
		{
			if (!function_exists('phpbb_get_user_avatar'))
			{
				require_once($this->phpbb_root_path . 'includes/functions.' . $this->php_ext);
			}

			$data = [
				'user_id' => (int) $user_id,
			];

			$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_build_array('SELECT', $data);
			$result = $this->db->sql_query($sql);
			$user_row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!$user_row)
			{
				return [];
			}

			$user_info = [
				'username' => $user_row['username'],
				'user_colour' => $user_row['user_colour'],
				'user_avatar' => phpbb_get_user_avatar($user_row),
				'user_posts' => $user_row['user_posts'],
				'user_regdate' => $user_row['user_regdate'],
				'user_avatar_w' => max(100, min($user_row['user_avatar_width'], 120)),
				'rank_title' => '',
				'rank_img_src' => '',
				'rank_img_alt' => '',
			];

			// i get RANK
			$user_rank_id = (int) $user_row['user_rank'];
			if ($user_rank_id > 0)
			{
				$data_rank = [
					'rank_id' => (int) $user_rank_id,
				];

				$sql_rank = 'SELECT rank_title, rank_image
				FROM ' . RANKS_TABLE . '
				WHERE ' . $this->db->sql_build_array('SELECT', $data_rank);
				$result_rank = $this->db->sql_query($sql_rank);
				$rank_row = $this->db->sql_fetchrow($result_rank);
				$this->db->sql_freeresult($result_rank);

				if ($rank_row)
				{
					$user_info['rank_title'] = $rank_row['rank_title'];
					if (!empty($rank_row['rank_image']))
					{
						$user_info['rank_img_src'] = $this->phpbb_root_path . 'images/ranks/' . $rank_row['rank_image'];
						$user_info['rank_img_alt'] = $rank_row['rank_title'];
					}
				}
			}

			// or RANK_NOT_SPECIAL
			else
			{
				$rank_search = [
					'rank_special' => 0,
				];

				$sql_rank = 'SELECT rank_title, rank_min, rank_image, rank_special
				FROM ' . RANKS_TABLE . '
				WHERE ' . $this->db->sql_build_array('SELECT', $rank_search);
				$result_rank = $this->db->sql_query($sql_rank);
				$rank_row = $this->db->sql_fetchrow($result_rank);
				$this->db->sql_freeresult($result_rank);

				if (!empty($rank_row) && isset($rank_row['rank_min'], $user_row['user_posts']) && $rank_row['rank_min'] < $user_row['user_posts'])
				{
					$user_info['rank_title'] = $rank_row['rank_title'];

					if (!empty($rank_row['rank_image']))
					{
						$user_info['rank_img_src'] = $this->phpbb_root_path . 'images/ranks/' . $rank_row['rank_image'];
						$user_info['rank_img_alt'] = $rank_row['rank_title'];
					}
				}
			}

			return $user_info;
		}

		protected function get_word_bbcode_count(string $text): int
		{
			$char_count = 0;
			// bbcode "invented" -> char -> pickup_char_number
			$board_lenght = mb_strlen($this->settings['board_url']);
			$bbcode_overhead_simple = [
			'b'     => 37,   // <strong class="text-strong"></strong>
			'i'     => 37,   // <em class="text-italics"></em>
			'u'     => 47,   // <span style="text-decoration:underline"></span>
			'url'   => 31 + $board_lenght,   // <a href="..." class="postlink">
			'img'   => 33,   // <img src="" width="" height="" />
			'quote' => 52,   // <blockquote class="uncited"><div></div></blockquote>
			'code'  => 133,  // <div class="codebox"><p>Code: <a href="#" onclick="selectCode(this); return false;">Select all</a></p><pre><code> </code></pre></div>
			'list'  => 9,    // <ul></ul>
			'*'     => 9,    // <li></li>
			];

			foreach ($bbcode_overhead_simple as $tag => $overhead_val)
			{
				$num_matches = preg_match_all('/\[' . preg_quote($tag, '/') . '(=[^\]]*)?\]/i', $text, $temp_matches);
				$char_count += $num_matches * $overhead_val;
			}

			// smilies from db
			$smilies_data = [];
			$sql = 'SELECT code, smiley_url, emotion
			FROM ' . SMILIES_TABLE;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$code = $row['code'];
				// 84 is <img class="smilies" src="./images/smilies/---url---" width="xx" height="xx" alt="---code---" title="---emotion---"> length
				$smiley_html_length = mb_strlen($row['smiley_url']) + mb_strlen($row['emotion']) + mb_strlen($row['code']) + 84;
				$smilies_data[$code] = $smiley_html_length;
			}
			$this->db->sql_freeresult($result);

			uksort($smilies_data, function($a, $b)
			{
				return mb_strlen($b) <=> mb_strlen($a);
			});

			foreach ($smilies_data as $code => $html_smiley_length)
			{
				$num_matches = preg_match_all('/' . preg_quote($code, '/') . '/', $text, $temp_matches);
				if ($num_matches > 0)
				{
					$overhead_per_smiley = $html_smiley_length - mb_strlen($code);
					$char_count += $num_matches * $overhead_per_smiley;
				}
			}
			return $char_count;
		}

		protected function get_post_info(int $post_id, string $mode): ?array
		{
			$data = [
				'post_id' => (int) $post_id,
			];

			$sql = 'SELECT post_subject, forum_id, topic_id, poster_id, post_text, bbcode_uid, bbcode_bitfield,
	        enable_bbcode, enable_smilies, enable_magic_url
	        FROM ' . POSTS_TABLE . '
	        WHERE ' . $this->db->sql_build_array('SELECT', $data);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!$row)
			{
				return null;
			}

			$counter_add = 0;

			if ($mode === 'signature')
			{
				if ($this->settings['view_bbcode_sig'] == 1)
				{
					$bbcode_options =
					(($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
					(($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) +
					(($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);

					$post_excerpt = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $bbcode_options);
					$counter_add = $this->get_word_bbcode_count($row['post_text']);
				}
				else
				{
					$post_excerpt = generate_text_for_edit($row['post_text'], $row['bbcode_uid'], 0);
					$post_excerpt = $post_excerpt['text'];
				}
			}
			else
			{
				// case1 full options
				// bbcode and smilies applies
				if ($this->settings['view_bbcode'] == 1)
				{
					$bbcode_options =
					(($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
					(($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) +
					(($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);

					$counter_add = $this->get_word_bbcode_count($row['post_text']);
					$post_excerpt = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $bbcode_options);
				}
				else if ($this->settings['view_bbcode'] == 0)
				{
					// case2 nothing applied
					$post_excerpt = generate_text_for_edit($row['post_text'], $row['bbcode_uid'], 0);
					$post_excerpt = $post_excerpt['text'];
				}
			}

			// cut it
			$max_length = intval($this->settings['car_length']) + $counter_add;
			if ($this->settings['car_length'] > 0 && mb_strlen($post_excerpt) > $max_length)
			{
				$truncated = mb_substr($post_excerpt, 0, $max_length);
				$last_space = mb_strrpos($truncated, ' ');
				if ($last_space !== false)
				{
					$truncated = mb_substr($truncated, 0, $last_space);
				}
				$post_excerpt = $truncated . '...';
			}

			$user_info = $this->get_user_info((int) $row['poster_id']);

			return [
				'post_subject' => $row['post_subject'],
				'forum_id' => (int) $row['forum_id'],
				'topic_id' => (int) $row['topic_id'],
				'post_excerpt' => $post_excerpt,
				'user_info' => $user_info,
			];
		}

		protected function get_topic_info(int $topic_id): ?array
		{
			$data = [
				'topic_id' => (int) $topic_id,
			];

			$sql = 'SELECT topic_first_post_id, topic_title
			FROM ' . TOPICS_TABLE . '
			WHERE ' . $this->db->sql_build_array('SELECT', $data);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!$row)
			{
				return null;
			}

			return [
				'topic_first_post_id' => (int) $row['topic_first_post_id'],
				'topic_title'         => $row['topic_title'],
			];
		}

		protected function link_edit(string $l2t_link, string $mode = 'postrow'): array
		{
			$replacements = [];
			$message = $l2t_link;
			$pattern = $this->settings['pattern'];

			if (preg_match_all($pattern, $message, $matches, PREG_SET_ORDER))
			{
				foreach ($matches as $match)
				{
					$script = '';
					$query_str = '';
					$anchor = '';
					$link_text = '';

					$script = $match[1];
					$query_str = $match[2];
					$anchor = isset($match[3]) ? ltrim($match[3], '#') : '';
					$link_text = $match[4];

					$parsed_board_url = parse_url($this->settings['board_url']);
					$full_url = 'https://' . $parsed_board_url['host'] . $this->settings['base_path'] . '/' . $script . '.php?' . $query_str;
					if (!empty($anchor))
					{
						$full_url .= '#' . $anchor;
					}
					$query_str = html_entity_decode($query_str);
					$full_url = html_entity_decode($full_url);

					parse_str($query_str, $params);

					$type = '';
 					$id = 0;
					if (isset($params['f']))
 					{
						$type = 'f';
						$id = (int) $params['f'];
 					}
					if (isset($params['t']))
 					{
 						$type = 't';
 						$id = (int) $params['t'];
 					}
					if (isset($params['p']))
 					{
						$type = 'p';
						$id = (int) $params['p'];
 					}

					if (!$type || !$id)
					{
						$replacements[] = [
						'original'        => $match[0],
						'full_url'        => $full_url,
						'type'            => '',
						'mode'            => $mode,
						'TPL_POST_FOUND'  => false,
						'TPL_FORUM_FOUND' => false,
						];
						continue;
					}

					$topic_id = null;
					$forum_id = null;
					$post_subject = '';
					$post_excerpt = '';
					$topic_title = '';
					$forum_name = '';
					$user_info = [];
					$l2t_date_format = '';

					if ($type === 'f')
					{
						$data_f = [
							'forum_id' => (int) $id,
						];
						$sql = 'SELECT forum_name
						FROM ' . FORUMS_TABLE . '
						WHERE ' . $this->db->sql_build_array('SELECT', $data_f);
						$result = $this->db->sql_query($sql);
						$row = $this->db->sql_fetchrow($result);
						$this->db->sql_freeresult($result);

						if ($row)
						{
							$forum_name = $row['forum_name'];
						}
					}

					if ($type === 'p')
					{
						$post_info = $this->get_post_info($id, $mode);
						$post_subject = $post_info['post_subject'] ?? '';
						$post_excerpt = $post_info['post_excerpt'] ?? '';
						$forum_id = $post_info['forum_id'] ?? 0;
						$topic_id = $post_info['topic_id'] ?? 0;
						$user_info = $post_info['user_info'] ?? [];
						$topic_title = $this->get_topic_info($topic_id)['topic_title'] ?? '';
					}

					if ($type === 't')
					{
						$topic_info = $this->get_topic_info($id);
						if ($topic_info)
						{
							$topic_title = $topic_info['topic_title'];
							$post_info = $this->get_post_info($topic_info['topic_first_post_id'], $mode);
							$post_subject = $post_info['post_subject'] ?? '';
							$post_excerpt = $post_info['post_excerpt'] ?? '';
							$forum_id = $post_info['forum_id'] ?? 0;
							$topic_id = $post_info['topic_id'] ?? 0;
							$user_info = $post_info['user_info'] ?? [];
						}
					}

					// Forum name
					if ($forum_id)
					{
						$data_f = [
							'forum_id' => (int) $forum_id,
						];
						$sql = 'SELECT forum_name
						FROM ' . FORUMS_TABLE . '
						WHERE ' . $this->db->sql_build_array('SELECT', $data_f);
						$result = $this->db->sql_query($sql);
						$row = $this->db->sql_fetchrow($result);
						$this->db->sql_freeresult($result);

						if ($row)
						{
							$forum_name = $row['forum_name'];
						}
					}

					// Date format
					$data_f = [
							'config_name' => 'default_dateformat',
						];
					$sql = 'SELECT config_value
					FROM ' . CONFIG_TABLE . '
					WHERE ' . $this->db->sql_build_array('SELECT', $data_f);
					$result = $this->db->sql_query($sql);
					$row = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);

					if ($row)
					{
						$l2t_date_format = str_replace("|", "", $row['config_value']);
					}

					$replacements[] = [
						'original'         => $match[0],
						'post_subject'     => $post_subject,
						'post_excerpt'     => $post_excerpt,
						'topic_title'      => $topic_title,
						'forum_name'       => $forum_name,
						'user_info'        => $user_info,
						'l2t_date_format'  => $l2t_date_format,
						'full_url'         => $full_url,
						'mode'             => $mode,
						'type'             => $type,
						'TPL_POST_FOUND'   => in_array($type, ['p', 't']) && !empty($post_subject),
						'TPL_FORUM_FOUND'  => $type === 'f' && !empty($forum_name),
					];
				}
			}

			return $replacements;
		}

		private function process_l2t_text(string $text, string $mode, $twig): string
		{
			$repls = $this->link_edit($text, $mode);

			foreach ($repls as $item)
			{
				if ($item['mode'] === 'signature')
				{
					$template_vars_settings = [
					'TPL_VIEW_USERNAME_SIG' => $this->settings['view_username_sig'],
					'TPL_VIEW_AVATAR_SIG'   => $this->settings['view_avatar_sig'],
					'TPL_VIEW_FORUM_SIG'    => $this->settings['view_forum_sig'],
					'TPL_VIEW_POPUP_SIG'    => $this->settings['view_popup_sig'],
					'TPL_VIEW_TEXT_SIG'     => $this->settings['view_text_sig'],
					];
				}
				else
				{
					$template_vars_settings = [
					'TPL_VIEW_USERNAME' => $this->settings['view_username'],
					'TPL_VIEW_AVATAR'   => $this->settings['view_avatar'],
					'TPL_VIEW_FORUM'    => $this->settings['view_forum'],
					'TPL_VIEW_POPUP'    => $this->settings['view_popup'],
					'TPL_VIEW_TEXT'     => $this->settings['view_text'],
					];
				}

				$popup_vars = array_merge($template_vars_settings, [
					'L2T_POST_SUBJECT'    => $item['post_subject'],
					'L2T_POST_EXCERPT'    => $item['post_excerpt'],
					'L2T_TOPIC_TITLE'     => $item['topic_title'],
					'L2T_FORUM_NAME'      => $item['forum_name'],
					'L2T_USER_AVATAR'     => $item['user_info']['user_avatar'] ?? '',
					'L2T_USER_AVATAR_W'   => $item['user_info']['user_avatar_w'] ?? '',
					'L2T_USER_POSTS'      => $item['user_info']['user_posts'] ?? '',
					'L2T_USER_REGDATE'    => isset($item['user_info']['user_regdate']) ? date($item['l2t_date_format'], $item['user_info']['user_regdate']) : '',
					'L2T_USER_COLOUR'     => $item['user_info']['user_colour'] ?? '',
					'L2T_USERNAME'        => $item['user_info']['username'] ?? '',
					'L2T_USER_RANK_TITLE' => $item['user_info']['rank_title'] ?? '',
					'L2T_RANK_IMG_SRC'    => $item['user_info']['rank_img_src'] ?? '',
					'L2T_RANK_IMG_ALT'    => $item['user_info']['rank_img_alt'] ?? '',
				]);

				$popup_html = $twig->render('@sebo_link2topicname/popup_preview.html', $popup_vars);

				$replacement_data = [
					'L2T_HREF'          => $item['full_url'],
					'TPL_POST_FOUND'    => $item['TPL_POST_FOUND'],
					'TPL_FORUM_FOUND'   => $item['TPL_FORUM_FOUND'],
				];

				if ($item['TPL_FORUM_FOUND'])
				{
					$replacement_data['L2T_FORUM_NAME'] = $item['forum_name'];
				}

				if ($item['TPL_POST_FOUND'])
				{
					$replacement_data = array_merge($replacement_data, [
						'L2T_POST_SUBJECT'  => $item['post_subject'],
						'L2T_TOPIC_TITLE'   => $item['topic_title'],
						'TPL_VIEW_POPUP'    => $this->settings['view_popup'],
						'L2T_POPUP_HTML'    => $popup_html,
					]);
				}
				if ($item['mode'] === 'signature')
				{
					if ($item['TPL_POST_FOUND'])
					{
						$replacement_data = array_merge($replacement_data, [
							'TPL_VIEW_POPUP_SIG'    => $this->settings['view_popup_sig'],
						]);
					}
				}				

				$template_name = ($item['mode'] === 'signature')
				? '@sebo_link2topicname/edit_signature_template.html'
				: '@sebo_link2topicname/edit_message_template.html';

				$link_html = $twig->render($template_name, $replacement_data);

				$text = str_replace($item['original'], $link_html, $text);
			}

			return $text;
		}

		public function edit_postrow($event)
		{
			$post_row = $event['post_row'];
			$message = $post_row['MESSAGE'];
			$signature = $post_row['SIGNATURE'];

			global $phpbb_container;
			$twig = $phpbb_container->get('template.twig.environment');

			// --- PARSE MESSAGE ---
			$message = $this->process_l2t_text($message, 'postrow', $twig);
			$post_row['MESSAGE'] = $message;
			// --- PARSE SIGNATURE ---
			if ($this->settings['enable_l2tsignature'])
			{
				$signature = $this->process_l2t_text($signature, 'signature', $twig);
				$post_row['SIGNATURE'] = $signature;
			}

			$event['post_row'] = $post_row;	
		}
	}						