<?php
defined('TYPO3_MODE') || die('Access denied.');


$_EXTKEY = 'mksanitizedparameters';

// sanitize in FE including eID
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][] = 
	'EXT:'.$_EXTKEY.'/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3.php:tx_mksanitizedparameters_hooks_PreprocessTypo3->sanitizeGlobalInputArrays';

// sanitize in BE
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/template.php']['preStartPageHook'][] = 
	'EXT:'.$_EXTKEY.'/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3.php:tx_mksanitizedparameters_hooks_PreprocessTypo3->sanitizeGlobalInputArrays';

$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['FE'] = array(
	'default'	=> FILTER_SANITIZE_STRING
	//@todo filter for alpha and alnum
	//'id'	=> ALNUM
);

$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['BE'] = array(
	'default'	=> FILTER_SANITIZE_STRING,
);
