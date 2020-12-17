<?php

namespace DMK\MkSanitizedParameters\Utility;

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

use DMK\MkSanitizedParameters\Rules;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Class to manipulate rules array for sanitizer.
 *
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class RulesUtility
{
    /**
     * @param array<string, int|string|array> $rules
     * @param string $nameToSanitize
     *
     * @return array<string, int|string|array>
     */
    public function getRulesForValue(array $rules, string $nameToSanitize): array
    {
        $rulesForValue = $this->getSpecialRulesByName($rules, $nameToSanitize);

        if (!$rulesForValue) {
            $rulesForValue = $this->getCommonRulesByName($rules, $nameToSanitize);
        }

        if (!$rulesForValue) {
            $rulesForValue = $rules[Rules::DEFAULT_RULES_KEY];
        }

        if (empty($rulesForValue)) {
            return [];
        }

        if (!is_array($rulesForValue)) {
            $rulesForValue = [$rulesForValue];
        }

        return $rulesForValue;
    }

    /**
     * @param array<string, int|string|array> $rules
     * @param string $nameToSanitize
     *
     * @return array<int|string, int|string|array>|null
     */
    private function getSpecialRulesByName(array $rules, string $nameToSanitize): ?array
    {
        $specialRules = null;

        if (!isset($rules[$nameToSanitize])) {
            return $specialRules;
        }

        $specialRules = $rules[$nameToSanitize];

        if (empty($specialRules)) {
            return null;
        }

        if (!is_array($specialRules)) {
            $specialRules = [$specialRules];
        }

        return $specialRules;
    }

    /**
     * @param array<string, int|string|array> $rules
     * @param string $nameToSanitize
     *
     * @return array<int|string, int|string|array>|null
     */
    private function getCommonRulesByName(array $rules, string $nameToSanitize): ?array
    {
        $commonRules = null;

        if (!(
            isset($rules[Rules::COMMON_RULES_KEY]) &&
            isset($rules[Rules::COMMON_RULES_KEY][$nameToSanitize])
        )) {
            return $commonRules;
        }

        $commonRules = $rules[Rules::COMMON_RULES_KEY][$nameToSanitize];

        if (empty($commonRules)) {
            return null;
        }

        if (!is_array($commonRules)) {
            $commonRules = [$commonRules];
        }

        return $commonRules;
    }

    /**
     * @param array<string, int|string|array> $rulesFromCurrentLevel
     * @param array<string, int|string|array> $rulesForNextLevel
     *
     * @return array<string, int|string|array>
     */
    public function injectFromCurrentIntoNextLevelIfNotSet(
        array $rulesFromCurrentLevel,
        array $rulesForNextLevel
    ): array {
        $rulesForValue = $rulesForNextLevel;

        $rulesForValue = $this->injectDefaultRulesFromCurrentIntoNextLevelIfNotSet(
            $rulesFromCurrentLevel,
            $rulesForValue
        );
        $rulesForValue = $this->injectCommonRulesFromCurrentIntoNextLevelIfNotSet(
            $rulesFromCurrentLevel,
            $rulesForValue
        );

        return $rulesForValue;
    }

    /**
     * @param array<string, int|string|array> $rulesFromCurrentLevel
     * @param array<string, int|string|array> $rulesForNextLevel
     *
     * @return array<string, int|string|array>
     */
    private function injectDefaultRulesFromCurrentIntoNextLevelIfNotSet(
        array $rulesFromCurrentLevel,
        array $rulesForNextLevel
    ): array {
        $rulesForNextLevel = $this->injectRulesByKey(
            $rulesForNextLevel,
            $rulesFromCurrentLevel,
            Rules::DEFAULT_RULES_KEY
        );

        return $rulesForNextLevel;
    }

    /**
     * @param array<string, int|string|array> $rulesFromCurrentLevel
     * @param array<string, int|string|array> $rulesForNextLevel
     *
     * @return array<string, int|string|array>
     */
    private function injectCommonRulesFromCurrentIntoNextLevelIfNotSet(
        array $rulesFromCurrentLevel,
        array $rulesForNextLevel
    ): array {
        $rulesForNextLevel = $this->injectRulesByKey(
            $rulesForNextLevel,
            $rulesFromCurrentLevel,
            Rules::COMMON_RULES_KEY
        );

        // we want to merge the common rules!
        if ($rulesFromCurrentLevel[Rules::COMMON_RULES_KEY]) {
            // the first array for ArrayUtility is handled as reference, so we create a new one!
            $commonRules = [Rules::COMMON_RULES_KEY => $rulesFromCurrentLevel[Rules::COMMON_RULES_KEY]];
            ArrayUtility::mergeRecursiveWithOverrule(
                $commonRules,
                $rulesForNextLevel
            );

            return $commonRules;
        }

        return $rulesForNextLevel;
    }

    /**
     * @param array<string, int|string|array> $rulesForValue
     * @param array<string, int|string|array> $allRules
     * @param string $rulesKey
     *
     * @return array<string, int|string|array>
     */
    private function injectRulesByKey(
        array $rulesForValue,
        array $allRules,
        string $rulesKey
    ): array {
        if (!array_key_exists($rulesKey, $rulesForValue)) {
            $rulesForValue[$rulesKey] = $allRules[$rulesKey];
        }

        return $rulesForValue;
    }
}
