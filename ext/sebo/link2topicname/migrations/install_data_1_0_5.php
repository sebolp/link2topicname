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

class install_data_1_0_5 extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return [
		'\sebo\link2topicname\migrations\install_data_1_0_3',
		'\sebo\link2topicname\migrations\install_schema_1_0_5',
		];
	}

	public function update_data()
	{
		return [
			['custom', [[$this, 'table_l2t_install_1_0_5']]],
		];
	}

	public function table_l2t_install_1_0_5()
	{
		$l2t_table_settings = $this->table_prefix . 'sebo_l2t_settings';
		if ($this->db_tools->sql_table_exists($l2t_table_settings) && $this->db_tools->sql_column_exists($l2t_table_settings, 'text_formatted_sig'))
		{
			$data_1_0_5 = [
			'enable_l2tsignature' => 1,
			'view_username_sig' => 1,
			'view_avatar_sig' => 1,
			'view_forum_sig' => 1,
			'view_popup_sig' => 1,
			'view_text_sig' => 1,
			'text_formatted_sig' => 1,
			];
			$sql = 'UPDATE ' . $l2t_table_settings . ' SET ' . $this->db->sql_build_array('UPDATE', $data_1_0_5);
			$this->db->sql_query($sql);
		}
	}
}
