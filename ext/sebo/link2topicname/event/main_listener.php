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

	use phpbb\textformatter\s9e\utils;

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
				'view_bbcode'    => (bool) $row['text_formatted']
				];
			}
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

		protected function get_word_bbcode_count($text)
		{
			// avoid too short for bbcode tags
			preg_match_all('/\[(.*?)\]/', $text, $matches);

			$char_count = 0;
			foreach ($matches[1] as $content_inside) {
				$full_text = '[' . $content_inside . ']';
				$char_count += strlen($full_text);
			}

			return $char_count;
		}

		protected function get_post_info(int $post_id): ?array
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

			// case1 full options
			// bbcode and smilies applies
			if ($this->settings['view_bbcode'] == 1)
			{
			$bbcode_options =
				(($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
				(($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) +
				(($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);

			$post_excerpt = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $bbcode_options);
			}
			else if ($this->settings['view_bbcode'] == 0)
			{
			// case2 nothing applied
			$post_excerpt = generate_text_for_edit($row['post_text'], $row['bbcode_uid'], $bbcode_options);
			$post_excerpt = $post_excerpt['text'];
			}

			// cut it
			$counter_add = $this->get_word_bbcode_count($post_excerpt);
			$max_length = $this->settings['car_length'] + $counter_add ?? 120;

			if ($max_length > 0 && mb_strlen($post_excerpt) > $max_length)
			{
				$post_excerpt = mb_substr($post_excerpt, 0, $max_length) . '...';
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

		public function edit_postrow($event)
		{
			$post_row = $event['post_row'];
			$message = $post_row['MESSAGE'];
			$board_url = generate_board_url();

			$parsed_board_url = parse_url($board_url);
			$host = preg_quote(preg_replace('~^www\.~i', '', $parsed_board_url['host']), '~');
			$base_path = preg_quote($parsed_board_url['path'] ?? '', '~'); // es: "/forum" oppure ""

			$pattern = '~<a[^>]+href="https?://(?:www\.)?' . $host . $base_path . '/(viewtopic|viewforum)\.php\?([^"#]*)(#[^"]*)?"[^>]*>(.*?)</a>~i';

			if (preg_match_all($pattern, $message, $matches, PREG_SET_ORDER))
			{
				foreach ($matches as $match)
				{
					$script = $match[1];
					$query_str = $match[2];
					$anchor = isset($match[3]) ? ltrim($match[3], '#') : '';
					$link_text = $match[4];

					$full_url = 'https://' . $parsed_board_url['host'] . $base_path . '/' . $script . '.php?' . $query_str;
					if ($anchor)
					{
						$full_url .= '#' . $anchor;
					}
					$full_url = html_entity_decode($full_url);

					parse_str($query_str, $params);

					$type = '';
					$id = 0;

					if (isset($params['p']))
					{
						$type = 'p';
						$id = (int) $params['p'];
					}
					else if (isset($params['t']))
					{
						$type = 't';
						$id = (int) $params['t'];
					}
					else if (isset($params['f']))
					{
						$type = 'f';
						$id = (int) $params['f'];
					}

					$anchor = $url_parts['fragment'] ?? '';

					$topic_id = null;
					$forum_id = null;
					$post_subject = '';
					$topic_title = '';
					$forum_name = '';
					$username = '';
					$user_colour = '';
					$user_rank = '';
					$user_avatar = '';
					$user_html = '';

					// Forum link
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
							$label = 'Forum: <em>' . htmlspecialchars($forum_name) . '</em>';
							$link_html = '<a href="' . htmlspecialchars($full_url) . '">' . $label . '</a>';
							$message = str_replace($match[0], $link_html, $message);
						}
						continue;
					}

					if ($type === 'p')
					{
					$post_info = $this->get_post_info($id);

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
						if (!$topic_info)
						{
							continue;
						}
						else if ($topic_info)
						{
							$post_info = $this->get_post_info($topic_info['topic_first_post_id']);
						}

						$post_subject = $post_info['post_subject'];
						$post_excerpt = $post_info['post_excerpt'];
						$forum_id = $post_info['forum_id'];
						$topic_id = $post_info['topic_id'];
						$user_info = $post_info['user_info'];
						$topic_title = $topic_info['topic_title'];

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

					$template_vars_settings = [
					'TPL_VIEW_USERNAME' => $this->settings['view_username'],
					'TPL_VIEW_AVATAR'   => $this->settings['view_avatar'],
					'TPL_VIEW_FORUM'    => $this->settings['view_forum'],
					'TPL_VIEW_POPUP'    => $this->settings['view_popup'],
					'TPL_VIEW_TEXT'     => $this->settings['view_text'],
					];

					$popup_vars = array_merge($template_vars_settings, [
					'L2T_POST_SUBJECT'   => $post_subject ?? '',
					'L2T_POST_EXCERPT'   => $post_excerpt ?? '',
					'L2T_TOPIC_TITLE'    => $topic_title ?? '',
					'L2T_FORUM_NAME'     => $forum_name ?? '',
					'L2T_USER_AVATAR'    => $user_info['user_avatar'] ?? '',
					'L2T_USER_AVATAR_W'  =>	$user_info['user_avatar_w'] ?? '',
					'L2T_USER_POSTS'    => $user_info['user_posts'] ?? '',
					'L2T_USER_REGDATE'  => isset($user_info['user_regdate']) ? date($l2t_date_format, $user_info['user_regdate']) : '',
					'L2T_USER_COLOUR'    => $user_info['user_colour'] ?? '',
					'L2T_USERNAME'       => $user_info['username'] ?? '',
					'L2T_USER_RANK_TITLE'=> $user_info['rank_title'] ?? '',
					'L2T_RANK_IMG_SRC'   => $user_info['rank_img_src'] ?? '',
					'L2T_RANK_IMG_ALT'   => $user_info['rank_img_alt'] ?? '',
					]);

					global $phpbb_container;
					$twig = $phpbb_container->get('template.twig.environment');

					$popup_html = $twig->render('@sebo_link2topicname/popup_preview.html', $popup_vars);

					$replacement_data = [
					'L2T_HREF'          => $full_url,
					'L2T_POST_SUBJECT'  => $post_subject,
					'L2T_TOPIC_TITLE'   => $topic_title,
					'TPL_VIEW_POPUP'=> $this->settings['view_popup'],
					'L2T_POPUP_HTML'    => $popup_html,
					'TPL_POST_FOUND' => (bool) $post_info
					];

					$link_html = $twig->render('@sebo_link2topicname/edit_message_template.html', $replacement_data);

					$message = str_replace($match[0], $link_html, $message);

				}
			}

			$post_row['MESSAGE'] = $message;
			$event['post_row'] = $post_row;
		}
	}
