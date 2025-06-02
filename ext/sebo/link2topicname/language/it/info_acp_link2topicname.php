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
	
	'ACP_link2topicname_EXPLAIN'	=> 'Attenzione: l\'estensione riconosce automaticamente dove è installato il tuo phpBB, ma potrebbe essere necessario inserire "Censura parole" da "Contenuti > Messaggi > Censura parole" per trasformare http:// in https:// o viceversa, ed includere o escludere www da tutti i link. Questo dipende dalle impostazioni di installazione del tuo forum.',
	
	'PP_ME'					=> 'Offrimi una birra per questa estensione',
	'PP_ME_EXT'				=> '<label>Fai una donazione per questa estensione:</label><br><span>Questa estensione è completamente gratuita. E\' un progetto su cui ho speso del tempo per imparare e condividere con la community phpBB. Se ti piace questa estensione, o ha migliorato il tuo forum, prendi in considerazione l\'idea di <a href="https://www.paypal.com/donate/?hosted_button_id=GS3T9MFDJJGT4" target="_blank" rel="noreferrer noopener">offrirmi una birra</a>. Grazie mille anche solo per aver scaricato link2topicname!</span>',
	'PP_ME_EXT_ALT'			=> 'Effettua una donazione con PayPal',

	'ACP_ENABLE_POPUP_TITLE'=> 'Abilita la visualizzazione del popup con l\'anteprima del messaggio',
	'ACP_SETTINGS_TITLE'	=> 'Impostazioni del popup di anteprima',
	
	'ACP_link2topicname_ENABLE_POPUP_REQ' => 'Vuoi abilitare la visualizzazione del popup di anteprima?',
	
	'ACP_link2topicname_CAR_LENGHT' => 'Imposta la lunghezza di caratteri dell\'anteprima messaggio',
	'ACP_link2topicname_VIEW_TEXT' => 'Vuoi visualizzare l\'anteprima del messaggio?',
	'ACP_link2topicname_VIEW_AVATAR' => 'Vuoi visualizzare l\'avatar?',
	'ACP_link2topicname_VIEW_FORUM' => 'Vuoi visualizzare il forum di provenienza?',
	'ACP_link2topicname_VIEW_USERNAME' => 'Vuoi visualizzare l\'autore del messaggio?',
	
	'LOG_ACP_link2topicname_SETTINGS'		=> '<strong>Impostazioni link2topicname aggiornate</strong>',
	
	'L2TN_UPDATED'						=> 'Impostazioni aggiornate',
	'L2TN_NOT_UPDATED'					=> 'Impostazioni non aggiornate',
	
	'LOG_ACP_link2topicname_SETTINGS' => 'Impostazioni Link2TopicName aggiornate',
	'RETURN_ACP' => 'Torna indietro al <a href="%s">pannello di controllo</a>',
]);
