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

	'ACP_LINK2TOPICNAME_EXPLAIN_ADV'	=> 'Attenzione:',
	'ACP_LINK2TOPICNAME_EXPLAIN'	=> 'l\'estensione riconosce automaticamente dove è installato il tuo phpBB, ma potrebbe essere necessario inserire "Censura parole" da ',
	'ACP_LINK2TOPICNAME_EXPLAIN_POSTING'	=> 'Contenuti',
	'ACP_LINK2TOPICNAME_EXPLAIN_MESSAGES'	=> 'Messaggi',
	'ACP_LINK2TOPICNAME_EXPLAIN_WC'	=> 'Censura parole',
	'ACP_LINK2TOPICNAME_EXPLAIN_FINAL'	=> 'per trasformare "http://" in "https://" o viceversa, ed includere o escludere "www" da tutti i link. Questo dipende dalle impostazioni di installazione del tuo forum.',
	'ACP_LINK2TOPICNAME_ARROW' => '<i class="fa icon fa-chevron-right fa-fw" aria-hidden="true"></i>',

	'PP_ME'					=> 'Offrimi una birra per questa estensione',
	'PP_ME_EXT_DONATE'		=> 'Fai una donazione per questa estensione',
	'PP_ME_EXT_FIRST'		=> 'Questa estensione è completamente gratuita. E\' un progetto su cui ho speso del tempo per imparare e condividere con la community phpBB. Se ti piace questa estensione, o ha migliorato il tuo forum, prendi in considerazione l\'idea di ',
	'PP_ME_EXT_OFFER'		=> 'offrirmi una birra!',
	'PP_ME_EXT_THX'		=> 'Grazie mille anche solo per aver scaricato LINK2TOPICNAME!',
	'PP_ME_EXT_ALT'			=> 'Effettua una donazione con PayPal',

	'ACP_ENABLE_POPUP_TITLE'=> 'Abilita la visualizzazione del popup con l\'anteprima del messaggio',
	'ACP_SETTINGS_TITLE'	=> 'Impostazioni del popup di anteprima',

	'ACP_LINK2TOPICNAME_ENABLE_POPUP_REQ' => 'Vuoi abilitare la visualizzazione del popup di anteprima?',

	'ACP_LINK2TOPICNAME_CAR_LENGTH' => 'Imposta la lunghezza di caratteri dell\'anteprima messaggio (0 vuol dire visualizzare l\'intero post)',
	'ACP_LINK2TOPICNAME_VIEW_TEXT' => 'Vuoi visualizzare l\'anteprima del messaggio?',
	'ACP_LINK2TOPICNAME_VIEW_AVATAR' => 'Vuoi visualizzare l\'avatar?',
	'ACP_LINK2TOPICNAME_VIEW_FORUM' => 'Vuoi visualizzare il forum di provenienza?',
	'ACP_LINK2TOPICNAME_VIEW_USERNAME' => 'Vuoi visualizzare l\'autore del messaggio?',

	'L2TN_UPDATED'						=> 'Impostazioni aggiornate',
	'L2TN_NOT_UPDATED'					=> 'Impostazioni non aggiornate',

	'LOG_ACP_LINK2TOPICNAME_SETTINGS' => 'Impostazioni LINK2TOPICNAME aggiornate',
	'RETURN_ACP' => 'Torna indietro al <a href="%s">pannello di controllo</a>',

	/* >=1_0_3 */
	'ACP_LINK2TOPICNAME_VIEW_BBCODE' => 'Vuoi visualizzare bbcodes, "magic_urls" e smilies nel popups?',
	/* >=1_0_5 */
	'ACP_MESSAGE_SUB_TITLE'			=> 'Impostazioni messaggio',
	'ACP_SIGNATURE_SUB_TITLE'			=> 'Impostazioni firma',
	'ACP_COMMON_SUB_TITLE'			=> 'Impostazioni globali',
	'ACP_LINK2TOPICNAME_ENABLE_SIG'	=> 'Attivare la sostituzione dei link nella firma?',
	'ACP_LINK2TOPICNAME_CAR_LENGTH_EXT' => 'Ricorda: il numero di caratteri impostati può differire leggermente a causa del bbcode',

]);
