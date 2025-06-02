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

class install_acp_module extends \phpbb\db\migration\migration
{

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v320\v320'];
	}

	public function update_data()
	{
		return [
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_link2topicname_TITLE'
			]],
			['module.add', [
				'acp',
				'ACP_link2topicname_TITLE',
				[
					'module_basename'	=> '\sebo\link2topicname\acp\main_module',
					'modes'				=> ['settings'],
				],
			]],
		];
	}
}
