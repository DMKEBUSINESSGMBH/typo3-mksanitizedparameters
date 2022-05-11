<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mksanitizedparameters" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

if (!defined('TYPO3')) {
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
