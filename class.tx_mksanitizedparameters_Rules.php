<?php

/***************************************************************
 * Copyright notice
 *
 * (c) 2020 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class to register and retrieve rules for
 * the parameters in the system.
 *
 * @author Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mksanitizedparameters_Rules
{
    /**
     * @var string
     */
    const COMMON_RULES_KEY = '__common';

    /**
     * @var string
     */
    const DEFAULT_RULES_KEY = '__default';

    /**
     * @var array
     */
    protected static $rulesForFrontend = [];

    /**
     * @var array
     */
    protected static $rulesForBackend = [];

    /**
     * look into the doc of
     * tx_mksanitizedparameters::sanitizeArrayByRules()
     * to see how the rules must be passed.
     *
     * @param array $rules
     */
    public static function addRulesForFrontend(
        array $rules
    ) {
        self::$rulesForFrontend =
            tx_rnbase_util_Arrays::mergeRecursiveWithOverrule(
                self::$rulesForFrontend,
                $rules
            );
    }

    /**
     * look into the doc of
     * tx_mksanitizedparameters::sanitizeArrayByRules()
     * to see how the rules must be passed.
     *
     * @param array $rules
     */
    public static function addRulesForBackend(
        array $rules
    ) {
        self::$rulesForBackend =
            tx_rnbase_util_Arrays::mergeRecursiveWithOverrule(
                self::$rulesForBackend,
                $rules
            );
    }

    /**
     * the default environment is Frontend.
     *
     * @return array
     */
    public static function getRulesForCurrentEnvironment()
    {
        switch (TYPO3_MODE) {
            case 'FE':
            default:
                $parameterRulesForCurrentEnvironment =
                    self::getRulesForFrontend();
                break;
            case 'BE':
                $parameterRulesForCurrentEnvironment =
                    self::getRulesForBackend();
                break;
        }

        return $parameterRulesForCurrentEnvironment;
    }

    /**
     * @return array
     */
    public static function getRulesForFrontend()
    {
        return self::$rulesForFrontend;
    }

    /**
     * @return array
     */
    public static function getRulesForBackend()
    {
        return self::$rulesForBackend;
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/util/class.tx_mksanitizedparameters_Rules.php']) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/util/class.tx_mksanitizedparameters_Rules.php'];
}
