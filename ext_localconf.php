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
// config below. examples for the config possibilities can be 
// found in class.tx_mksanitizedparameters.php. You can also check
// the testcases in /tests to see how the classes work.
$defaultExtConfig = array(
	'parameterRules' => array(
		'FE' => array(
			'default'	=> FILTER_SANITIZE_STRING,
			//@todo filter for alpha and alnum
			//'id'	=> ALNUM
		),
		'BE' => array(
			'default'	=> FILTER_SANITIZE_STRING,
		)
	)
);

$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY] = 
	t3lib_div::array_merge_recursive_overrule(
		$defaultExtConfig, $TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]
	);