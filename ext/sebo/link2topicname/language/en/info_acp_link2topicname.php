<?php
/**
 *
 * l2topicname. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, sebo, https://www.fiatpandaclub.org
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'ACP_LINK2TOPICNAME_TITLE'	=> 'Link 2 Topic Name',
	'ACP_LINK2TOPICNAME'			=> 'L2TN Settings',

	'ACP_LINK2TOPICNAME_EXPLAIN_ADV'	=> 'Please note:',
	'ACP_LINK2TOPICNAME_EXPLAIN'	=> 'the extension automatically detects where your phpBB is installed, but you may need to edit "Word censoring" from',
	'ACP_LINK2TOPICNAME_EXPLAIN_POSTING'	=> 'Posting',
	'ACP_LINK2TOPICNAME_EXPLAIN_MESSAGES'	=> 'Messages',
	'ACP_LINK2TOPICNAME_EXPLAIN_WC'	=> 'Word censoring',
	'ACP_LINK2TOPICNAME_EXPLAIN_FINAL'	=> 'to change "http://" to "https://" or vice versa, and include or exclude "www" from all links. This depends on your board installation settings.',
	'ACP_LINK2TOPICNAME_ARROW' => '<i class="fa icon fa-chevron-right fa-fw" aria-hidden="true"></i>',

	'PP_ME'					=> 'Buy me a beer for creating this extension',
	'PP_ME_EXT_DONATE'		=> 'Make a donation for this extension',
	'PP_ME_EXT_FIRST'		=> 'This extension is completely free. It is a project into wich i\'ve spent my time to learn code, have fun and for the phpBB community. If you enjoy using this extension, or if it has benefited your forum, please consider ',
	'PP_ME_EXT_OFFER'		=> 'buying me a beer!',
	'PP_ME_EXT_THX'		=> 'It would be greatly appreciated. Thank you for downloading LINK2TOPICNAME!',
	'PP_ME_EXT_ALT'			=> 'Donate via PayPal',

	'ACP_ENABLE_POPUP_TITLE'     => 'Enable post popup display',
	'ACP_SETTINGS_TITLE'         => 'Message preview popup settings',

	'ACP_LINK2TOPICNAME_ENABLE_POPUP_REQ' => 'Do you want to display the post popup preview?',

	'ACP_LINK2TOPICNAME_CAR_LENGTH'       => 'Set the character length of the post preview (0 means full post)',
	'ACP_LINK2TOPICNAME_VIEW_TEXT'        => 'Do you want to display the message preview?',
	'ACP_LINK2TOPICNAME_VIEW_AVATAR'      => 'Do you want to display the poster avatar?',
	'ACP_LINK2TOPICNAME_VIEW_FORUM'       => 'Do you want to display the source forum?',
	'ACP_LINK2TOPICNAME_VIEW_USERNAME'    => 'Do you want to display the original poster?',

	'L2TN_UPDATED'						=> 'Settings updated',
	'L2TN_NOT_UPDATED'					=> 'Settings not updated',

	'LOG_ACP_LINK2TOPICNAME_SETTINGS' => 'LINK2TOPICNAME settings updated',
	'RETURN_ACP' => 'Return to the <a href="%s">Control Panel</a>',

]);
