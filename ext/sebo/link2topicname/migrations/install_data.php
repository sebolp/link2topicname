<?php
/**
 *
 * link2topicname. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, sebo, https://www.fiatpandaclub.org - Thanks Chris1278!
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */
namespace sebo\link2topicname\migrations;

class install_data extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return ['\sebo\link2topicname\migrations\install_schema'];
	}

	public function update_data()
	{
		return [
			['custom', [[$this, 'table_l2t_install']]],
		];
	}
	
	public function table_l2t_install()
	{
		$data = [
					[
					'car_length'    => 120,
					'view_username'       => 1,
					'view_avatar'      => 1,
					'view_forum'    => 1,
					'view_popup'   => 1,
					'view_text'     => 1,
					]
				];
		$this->db->sql_multi_insert($this->table_prefix . 'sebo_l2t_settings', $data);
	}

}
