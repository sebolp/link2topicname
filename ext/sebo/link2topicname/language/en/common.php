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
	/* >=1_0_3 */
	'L2T_ALERT_NO_LINK' => 'OOOOPS, something wrong',
	'L2T_ALERT_NO_LINK_EXPLAIN' => 'the linked post does not exist!',
]);
