<?php
/**
	*
	* link2topicname. An extension for the phpBB Forum Software package.
	*
	* @copyright (c) 2025, sebo, https://www.fiatpandaclub.org
	* @license GNU General Public License, version 2 (GPL-2.0)
	*
*/

	namespace sebo\link2topicname\migrations;

	class install_schema_1_0_5 extends \phpbb\db\migration\migration
	{

		public function effectively_installed()
		{
			$l2tn_table = $this->table_prefix . 'sebo_l2t_settings';

			if (!$this->db_tools->sql_table_exists($l2tn_table))
			{
				return false;
			}
			return $this->db_tools->sql_column_exists($l2tn_table, 'text_formatted_sig');
		}

		public static function depends_on()
		{
			return ['\sebo\link2topicname\migrations\install_schema_1_0_3'];
		}

		public function update_schema()
		{
			return [
				'add_columns' => [
					$this->table_prefix . 'sebo_l2t_settings' => [
							'enable_l2tsignature'	=> ['UINT', 1],
							'view_username_sig'		=> ['UINT', 1],
							'view_avatar_sig'		=> ['UINT', 1],
							'view_forum_sig'		=> ['UINT', 1],
							'view_popup_sig'		=> ['UINT', 1],
							'view_text_sig'		=> ['UINT', 1],
							'text_formatted_sig'		=> ['UINT', 1],
					],
				],
			];
		}

		public function revert_schema()
		{
			return [
				'drop_columns' => [
					$this->table_prefix . 'sebo_l2t_settings' => [
						'enable_l2tsignature',
						'view_username_sig',
						'view_avatar_sig',
						'view_forum_sig',
						'view_popup_sig',
						'view_text_sig',
						'text_formatted_sig',
					],
				],
			];
		}
	}
