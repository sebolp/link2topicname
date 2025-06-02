<?php
/**
 *
 * link2topicname. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, sebo, https://www.fiatpandaclub.org
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace sebo\link2topicname\acp;

/**
 * link2topicname ACP module info.
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\sebo\link2topicname\acp\main_module',
			'title'		=> 'ACP_link2topicname_TITLE',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'ACP_link2topicname',
					'auth'	=> 'ext_sebo/link2topicname && acl_a_board',
					'cat'	=> ['ACP_link2topicname_TITLE'],
				],
			],
		];
	}
}
