<?php
defined('TYPO3_MODE') || die('Access denied.');


$_EXTKEY = 'mksanitizedparameters';

// sanitize in FE including eID
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][] = 
	'EXT:'.$_EXTKEY.'/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php:tx_mksanitizedparameters_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays';

// sanitize in BE
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/template.php']['preStartPageHook'][] = 
	'EXT:'.$_EXTKEY.'/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php:tx_mksanitizedparameters_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays';

// the default config for common TYPO3 request parameters.
// add your own parameter rules in localconf.php similar to the
// config below or overwrite them. examples for the config possibilities  
// can be found in class.tx_mksanitizedparameters.php. You can also check
// the testcases in /tests to see how the classes work.
$defaultExtConfig = array(
	'parameterRules' => array(
		'FE' => array(
			'default'	=> array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_ENCODED)
		),
		'BE' => array(
			'default'	=> array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_ENCODED)
		)
	)
);

$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY] = 
	t3lib_div::array_merge_recursive_overrule(
		$defaultExtConfig, (array) $TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]
	);