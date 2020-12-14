<?php

if (defined('TYPO3_MODE')) {
    exit('Access denied.');
}

// register old tslib and template hooks. after TYPO3 9 we use a middleware!
if (!\DMK\MkSanitizedParameters\Utility\Typo3Utility::isTypo3Version9OrHigher()) {
    // sanitize in FE including eID
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['determineId-PostProc'][] =
        \DMK\MkSanitizedParameters\Hook\Typo3RequestsHook::class.'->sanitizeGlobalInputArrays';

    // sanitize in BE
    // wir setzen einen eindeutigen Key um diesen in den Tests überschreiben zu können
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/template.php']['preStartPageHook']['mksanitizedparameters'] =
        \DMK\MkSanitizedParameters\Hook\Typo3RequestsHook::class.'->sanitizeGlobalInputArrays';
}

\DMK\MkSanitizedParameters\Rules::loadDefaults();
