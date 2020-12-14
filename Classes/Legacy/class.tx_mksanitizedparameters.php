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
 * Class to sanitize an array through the filter_var method.
 * Therefore the rules are based on the one for
 * filter_var_array. The rules array mirrors the array
 * to be sanitized.
 * In difference to filter_var_array this class supports
 * multi dimensional arrays, common values for recurring values,
 * default values for unconfigured parameters and multiple filters per value.
 *
 * for all possibilities look into the doc block of sanitizeArrayByRules
 *
 * @deprecated use \DMK\MkSanitizedParameters\Sanitizer instead
 *
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 *
 * @SuppressWarnings(PHPMD)
 */
class tx_mksanitizedparameters extends \DMK\MkSanitizedParameters\Sanitizer
{
    /**
     * See Sanitizer::sanitizeArrayByRules.
     *
     * @param array $arrayToSanitize
     * @param array $rules
     *
     * @return array
     */
    public function sanitizeArrayByRules(
        array $arrayToSanitize,
        array $rules
    ): array {
        return parent::sanitizeArrayByRules($arrayToSanitize, $rules);
    }

    /**
     * @param array $arraysToSanitize
     * @param array $rules
     */
    public function sanitizeArraysByRules(
        array &$arraysToSanitize,
        array $rules
    ) {
        foreach ($arraysToSanitize as $arrayName => &$arrayToSanitize) {
            $arrayToSanitize = $this->sanitizeArrayByRules(
                $arrayToSanitize,
                $rules
            );
        }
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/class.tx_mksanitizedparameters.php']) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/class.tx_mksanitizedparameters.php'];
}
