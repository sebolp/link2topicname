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

	class install_schema extends \phpbb\db\migration\migration
	{
	
		public static function depends_on()
		{
			return ['\phpbb\db\migration\data\v320\v320'];
		}
	
		public function update_schema()
		{
			return [
			'add_tables' => [
			$this->table_prefix . 'sebo_l2t_settings' => [
			'COLUMNS' => [
			'car_length'     => ['UINT:4', 120],
			'view_username'  => ['UINT:1', 1],
			'view_avatar'    => ['UINT:1', 1],
			'view_forum'     => ['UINT:1', 1],
			'view_popup'     => ['UINT:1', 1],
			'view_text'      => ['UINT:1', 1],
			],
			],
			],
			];
		}
	
		public function revert_schema()
		{
			return [
			'drop_tables'    => [
			$this->table_prefix . 'sebo_l2t_settings',
			],
			];
		}
	}
