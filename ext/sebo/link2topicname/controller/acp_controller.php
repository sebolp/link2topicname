<?php
/**
 *
 * link2topicname. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, sebo, https://www.fiatpandaclub.org
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace sebo\link2topicname\controller;

/**
 * link2topicname ACP controller.
 */
class acp_controller
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string Custom form action */
	protected $u_action;

	/** @var table_prefix */
	protected $table_prefix;

	/** @var \phpbb\user */
	protected $user;

	public function __construct
	(
	\phpbb\db\driver\driver_interface $db,
	\phpbb\language\language $language,
	\phpbb\log\log $log,
	\phpbb\request\request $request,
	\phpbb\template\template $template,
	$table_prefix,
	\phpbb\user $user
	)
	{
		$this->db		= $db;
		$this->language	= $language;
		$this->log		= $log;
		$this->request	= $request;
		$this->template	= $template;
		$this->table_prefix = $table_prefix;
		$this->user = $user;
	}

	public function display_options()
	{
		$this->language->add_lang('common', 'sebo/link2topicname');

		// Create a form key for preventing CSRF attacks
		add_form_key('sebo_link2topicname_acp');

		// Create an array to collect errors that will be output to the user
		$errors = [];

		// settings variables to template
		$sql = 'SELECT * FROM ' . $this->table_prefix . 'sebo_l2t_settings';
		$result = $this->db->sql_query($sql);
		$settings = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$template_vars_settings = [
			'TPL_VIEW_USERNAME' => (bool) $settings['view_username'],
			'TPL_VIEW_AVATAR'   => (bool) $settings['view_avatar'],
			'TPL_VIEW_FORUM'    => (bool) $settings['view_forum'],
			'TPL_VIEW_POPUP'    => (bool) $settings['view_popup'],
			'TPL_VIEW_TEXT'     => (bool) $settings['view_text'],
			'TPL_VIEW_BBCODE'     => (bool) $settings['text_formatted'],
			'TPL_CAR_LENGTH'    => (int) $settings['car_length'],
			'TPL_ENABLE_SIG' => (bool) $settings['enable_l2tsignature'],
			'TPL_VIEW_USERNAME_SIG' => (bool) $settings['view_username_sig'],
			'TPL_VIEW_AVATAR_SIG'   => (bool) $settings['view_avatar_sig'],
			'TPL_VIEW_FORUM_SIG'    => (bool) $settings['view_forum_sig'],
			'TPL_VIEW_POPUP_SIG'    => (bool) $settings['view_popup_sig'],
			'TPL_VIEW_TEXT_SIG'     => (bool) $settings['view_text_sig'],
			'TPL_VIEW_BBCODE_SIG'     => (bool) $settings['text_formatted_sig'],
		];

		$this->template->assign_vars($template_vars_settings);

		// Is the form being submitted to us?
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('sebo_link2topicname_acp'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// If no errors, process the form data
			if (empty($errors))
			{
				// request variables
				$view_username = (int) $this->request->variable('view_username', 1);
				$view_forum    = (int) $this->request->variable('view_forum', 1);
				$view_popup    = (int) $this->request->variable('view_popup', 1);
				$view_text     = (int) $this->request->variable('view_text', 1);
				$view_avatar   = (int) $this->request->variable('view_avatar', 1);
				$view_bbcode   = (int) $this->request->variable('view_bbcode', 1);
				$enable_l2tsignature = (int) $this->request->variable('enable_sig', 1);
				$view_username_sig = (int) $this->request->variable('view_username_sig', 1);
				$view_forum_sig    = (int) $this->request->variable('view_forum_sig', 1);
				$view_popup_sig    = (int) $this->request->variable('view_popup_sig', 1);
				$view_text_sig     = (int) $this->request->variable('view_text_sig', 1);
				$view_avatar_sig   = (int) $this->request->variable('view_avatar_sig', 1);
				$view_bbcode_sig   = (int) $this->request->variable('view_bbcode_sig', 1);
				$car_length    = (int) $this->request->variable('car_length', 120);

				$sql = 'UPDATE `' . $this->table_prefix . 'sebo_l2t_settings`
					SET `view_username` = ' . $view_username . ',
						`view_forum` = ' . $view_forum . ',
						`view_popup` = ' . $view_popup . ',
						`view_text` = ' . $view_text . ',
						`view_avatar` = ' . $view_avatar . ',
						`text_formatted` = ' . $view_bbcode . ',
						`view_username_sig` = ' . $view_username_sig . ',
						`view_forum_sig` = ' . $view_forum_sig . ',
						`view_popup_sig` = ' . $view_popup_sig . ',
						`view_text_sig` = ' . $view_text_sig . ',
						`view_avatar_sig` = ' . $view_avatar_sig . ',
						`text_formatted_sig` = ' . $view_bbcode_sig . ',
						`enable_l2tsignature` = ' . $enable_l2tsignature . ',
						`car_length` = ' . $car_length;

				$this->db->sql_query($sql);

				// Option settings have been updated
				// Confirm this to the user and provide (automated) link back to previous page
				meta_refresh(3, $this->u_action);
				$message = $this->language->lang('L2TN_UPDATED') . '<br /><br />' . $this->language->lang('RETURN_ACP', $this->u_action);
				// Add option settings change action to the admin log
				$this->log->add(
					'admin',
					(int) $this->user->data['user_id'],
					$this->user->data['user_ip'],
					'LOG_ACP_LINK2TOPICNAME_SETTINGS',
					time(),
					[]
				);
				trigger_error($message);
			}
			else
			{
				meta_refresh(3, $this->u_action);
				$message = $this->language->lang('L2TN_NOT_UPDATED');
				$errors[] = $message;
			}

		}

		$s_errors = !empty($errors);

		// Set output variables for display in the template
		$this->template->assign_vars([
			'S_ERROR'		=> $s_errors,
			'ERROR_MSG'		=> $s_errors ? implode('<br />', $errors) : '',

			'U_ACTION'		=> $this->u_action,
		]);
	}

	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
