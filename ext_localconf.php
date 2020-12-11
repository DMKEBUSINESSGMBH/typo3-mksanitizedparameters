<?php

defined('TYPO3_MODE') || exit('Access denied.');

// sanitize in FE including eID
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['determineId-PostProc'][] =
    'tx_mksanitizedparameters_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays';

// sanitize in BE
// wir setzen einen eindeutigen Key um diesen in den Tests überschreiben zu können
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/template.php']['preStartPageHook']['mksanitizedparameters'] =
    'tx_mksanitizedparameters_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays';

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mksanitizedparameters', 'ext_rules.php');
