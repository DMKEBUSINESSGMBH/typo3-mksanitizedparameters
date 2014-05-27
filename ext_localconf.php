<?php
defined('TYPO3_MODE') || die('Access denied.');

$_EXTKEY = 'mksanitizedparameters';

// sanitize in FE including eID
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][] =
	'EXT:'.$_EXTKEY.'/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php:tx_mksanitizedparameters_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays';

// sanitize in BE
// wir setzen einen eindeutigen Key um diesen in den Tests überschreiben zu können
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/template.php']['preStartPageHook']['mksanitizedparameters'] =
	'EXT:'.$_EXTKEY.'/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php:tx_mksanitizedparameters_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays';

require_once(t3lib_extMgm::extPath($_EXTKEY).'ext_rules.php');