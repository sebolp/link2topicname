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

class install_data_1_0_3 extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return [
        '\sebo\link2topicname\migrations\install_data',
        '\sebo\link2topicname\migrations\install_schema_1_0_3',
		];
	}

	public function update_data()
	{
		return [
			['custom', [[$this, 'table_l2t_install_1_0_3']]],
		];
	}

	public function table_l2t_install_1_0_3()
	{
		$l2t_table_settings = $this->table_prefix . 'sebo_l2t_settings';
		if ($this->db_tools->sql_table_exists($l2t_table_settings) &&
            $this->db_tools->sql_column_exists($l2t_table_settings, 'text_formatted')) {
			$data_1_0_3 = [
				['text_formatted' => 1],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'sebo_l2t_settings', $data_1_0_3);
		}
	}
}
