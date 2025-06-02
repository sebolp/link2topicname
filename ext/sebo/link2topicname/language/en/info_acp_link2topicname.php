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
	'ACP_link2topicname_TITLE'	=> 'Link 2 Topic Name',
	'ACP_link2topicname'			=> 'L2TN Settings',

	'ACP_link2topicname_EXPLAIN'	=> 'Please note: the extension automatically detects where your phpBB is installed, but you may need to edit "Word censoring" from "Posting > Messages > Word censoring" to change http:// to https:// or vice versa, and include or exclude www from all links. This depends on your board installation settings.',

	'PP_ME'					=> 'Buy me a beer for creating this extension',
	'PP_ME_EXT'				=> '<label>Make a donation for this extension:</label><br><span>This extension is completely free. It is a project that I spend my time on for the enjoyment and use of the phpBB community. If you enjoy using this extension, or if it has benefited your forum, please consider <a href="https://www.paypal.com/donate/?hosted_button_id=GS3T9MFDJJGT4" target="_blank" rel="noreferrer noopener">buying me a beer</a>. It would be greatly appreciated. Thank you for downloading Link2TopicName!</span>',
	'PP_ME_EXT_ALT'			=> 'Donate via PayPal',

	'ACP_ENABLE_POPUP_TITLE'     => 'Enable post popup display',
	'ACP_SETTINGS_TITLE'         => 'Message preview popup settings',

	'ACP_link2topicname_ENABLE_POPUP_REQ' => 'Do you want to display the post popup preview?',

	'ACP_link2topicname_CAR_LENGTH'       => 'Set the character length of the post preview (0 means full post)',
	'ACP_link2topicname_VIEW_TEXT'        => 'Do you want to display the message preview?',
	'ACP_link2topicname_VIEW_AVATAR'      => 'Do you want to display the poster avatar?',
	'ACP_link2topicname_VIEW_FORUM'       => 'Do you want to display the source forum?',
	'ACP_link2topicname_VIEW_USERNAME'    => 'Do you want to display the original poster?',

	'L2TN_UPDATED'						=> 'Settings updated',
	'L2TN_NOT_UPDATED'					=> 'Settings not updated',

	'LOG_ACP_link2topicname_SETTINGS' => 'Link2TopicName settings updated',
	'RETURN_ACP' => 'Return to the <a href="%s">Control Panel</a>',

]);
