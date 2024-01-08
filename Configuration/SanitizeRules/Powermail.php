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

// $rulesForFrontendVersion1
DMK\MkSanitizedParameters\Rules::addRulesForFrontend(
    [
        'tx_powermail_pi1' => [
            'url' => FILTER_SANITIZE_URL,
            // JSON String
            '__hmac' => FILTER_UNSAFE_RAW,
            '__trustedProperties' => FILTER_UNSAFE_RAW,
            '__referrer' => [
                'extensionName' => FILTER_SANITIZE_SPECIAL_CHARS,
                'controllerName' => FILTER_SANITIZE_SPECIAL_CHARS,
                'actionName' => FILTER_SANITIZE_SPECIAL_CHARS,
            ],
            'form' => FILTER_SANITIZE_NUMBER_INT,
            'field' => [
                DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => FILTER_UNSAFE_RAW,
            ],
        ],
    ]
);
