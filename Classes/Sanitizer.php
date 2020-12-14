<?php

declare(strict_types=1);

namespace DMK\MkSanitizedParameters;

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

use DMK\MkSanitizedParameters\Input\InputInterface;
use DMK\MkSanitizedParameters\Utility\DebugUtility;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class Sanitizer
{
    /**
     * @var string
     */
    const MESSAGE_VALUE_HAS_CHANGED = 'Ein Wert wurde von mksanitizedparameters verändert!';

    public function sanitizeInput(InputInterface ...$inputs): void
    {
        foreach ($inputs as $input) {
            if (!$input->isSanitizingNecessary()) {
                return;
            }

            $input->setCleanedInputArray(
                $this->sanitizeArrayByRules(
                    $input->getInputArray(),
                    $this->getRules()
                )
            );
        }
    }

    protected function getRules(): array
    {
        return Rules::getRulesForCurrentEnvironment();
    }

    /**
     * @param array $arrayToSanitize
     * @param array $rules
     *
     * @return array
     *
     * Sample rules:
     *
     * the order of the rules priority is the following:
     *  - special rules
     *  - common rules
     *  - default rules
     *
     * array(
     *
     *  // special parameters configuration.
     *  // will be used first
     *  // NOTE: it is not possible to have a configuration for the value
     *  // myParameterQualifier and for the sub parameters in myParameterQualifier.
     *  // only one is possible! either myParameterQualifier or myParameterQualifier[...]
     *  'myParameterQualifier' => array(
     *      'uid' => FILTER_SANITIZE_NUMBER_INT
     *      'searchWord' => array(
     *          'filter' => array(
     *              FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
     *          ),
     *          'flags' => FILTER_FLAG_ENCODE_AMP
     *      ),
     *      'subArray' => array(
     *          //so all unconfigured parameters inside subArray will get
     *          //the following default sanitization
     *          '__default'     => FILTER_SANITIZE_NUMBER_INT
     *
     *          //that's the way to call a custom filter!
     *          //make sure to have your custom class autoloaded.
     *          'someValue' => array(
     *              'filter'    => FILTER_CALLBACK,
     *              'options'   => array(
     *                  \DMK\MkSanitizedParameters\Sanitizer\AlphaSanitizer::class,'sanitizeValue'
     *              )
     *          )
     *      )
     *  )
     *
     *  // common parameters configuration
     *  // will be used if no special configuration found for a parameter name
     *  // can be inside a special rule, too.
     *  // will be handed down to subsequent levels. existing parameter name configurations
     *  // in subsequent levels have a higher priority and will not be overwritten.
     *  '__common' => array(
     *      // no matter at which position every parameter with the name someOtherValueToo
     *      // will be sanitized with the following configuration as long as there is no
     *      // special configuration
     *      someOtherValueToo => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)
     *  ),
     *  'myExt' => array(
     *      // this will add the common rules for everything inside myExt
     *      // so the cmmon config for otherValueToo is availabe, too
     *      '__common' => array(
     *          someOtherValueToo => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)
     *      ),
     *  ),
     *  '__common' => array(
     *      otherValueToo => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)
     *  ),
     *
     *  // default parameters configuration
     *  // will be used if no special and no common configuration is found for a parameter name
     *  // can be inside a special rule, too.
     *  // will be handed down to subsequent levels if there is no default configuration
     *  '__default' => FILTER_SANITIZE_STRING
     *  //OR
     *  '__default' => array(
     *      'filter' => array(
     *          FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
     *      ),
     *      'flags' => FILTER_FLAG_ENCODE_AMP
     *  )
     *  //OR
     *  '__default' => array(
     *      FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
     *  ),
     *  //OR
     *  'myExt => array(
     *      // this will overwrite the default rules for everything inside myExt
     *      default' => array(
     *          FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
     *      ),
     *  ),
     * '__default' => FILTER_SANITIZE_STRING
     * )
     *
     * for the following array:
     *
     * array(
     *  'myParameterQualifier' => array(
     *      'uid' => 1
     *      'searchWord' => 'johndoe',
     *      'subArray' => array(
     *          'someOtherValue' => ...
     *          'someOtherValueToo' => ...
     *      ),
     *      'someOtherValueToo' => ...
     *  )
     * )
     *
     * results in following sanitizing:
     *
     * array(
     *  'myParameterQualifier' => array(
     *      'uid' => the special rule will be used
     *      'searchWord' => the special rule will be used,
     *      'subArray' => array(
     *          'someOtherValue' => the default rule will be used
     *          'someOtherValueToo' => the common rule will be used
     *      ),
     *      'someOtherValueToo' => the common rule will be used
     *  )
     * )
     *
     * Attention:
     * Filter Configs with an array containing the key "filter" have higher priority
     * than filter configs containing array of multiple filters.
     * Single Filter Configs without array have highest priority.
     * So when you have a common config
     *  '__common' => array(
     *      'commonValue' => array(
     *          FILTER_SANITIZE_NUMBER_INT,....
     *      )
     *  )
     * that is overwritten in a lower level with
     * '__common' => array(
     *      'commonValue' => array(
     *          'filter' => array(
     *              FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
     *          ),
     *          'flags' => FILTER_FLAG_ENCODE_AMP
     *      )
     *  )
     * and again is overwritten in a lower level with
     * '__common' => array(
     *      'commonValue' => array(
     *          FILTER_SANITIZE_NUMBER_INT,....
     *      )
     * )
     * that config that is used in the last level is the following:
     * '__common' => array(
     *      'commonValue' => array(
     *          'filter' => array(
     *              FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
     *          ),
     *          'flags' => FILTER_FLAG_ENCODE_AMP
     *      )
     *  )
     * So you would need to declare the last config as followed:
     * '__common' => array(
     *      'commonValue' => array(
     *          'filter' => array(
     *              FILTER_SANITIZE_NUMBER_INT
     *          )
     *      )
     *  )
     * //OR
     * '__common' => array(
     *      'commonValue' => FILTER_SANITIZE_NUMBER_INT
     *  )
     */
    protected function sanitizeArrayByRules(
        array $arrayToSanitize,
        array $rules
    ): array {
        if (empty($rules)) {
            return $arrayToSanitize;
        }

        $filter = Factory::getFilter();

        foreach ($arrayToSanitize as $nameToSanitize => &$valueToSanitize) {
            $initialValueToSanitize = $valueToSanitize;

            $rulesForValue = $this->getRulesForValue(
                $rules,
                $nameToSanitize
            );

            if (is_array($valueToSanitize)) {
                // so we have them on the next level, too
                $rulesForValue = $this->injectDefaultRulesFromCurrentIntoNextLevelIfNotSet(
                    $rules,
                    $rulesForValue
                );
                $rulesForValue = $this->injectCommonRulesFromCurrentIntoNextLevelIfNotSet(
                    $rules,
                    $rulesForValue
                );
                $valueToSanitize = $this->sanitizeArrayByRules(
                    $valueToSanitize,
                    $rulesForValue
                );
            } elseif (!empty($rulesForValue)) {
                $valueToSanitize = $filter->sanitizeByRule(
                    $valueToSanitize,
                    $rulesForValue
                );
            }

            if ($filter->isValueChanged($initialValueToSanitize, $valueToSanitize)) {
                $this->handleDebugging(
                    $arrayToSanitize,
                    $nameToSanitize,
                    $initialValueToSanitize,
                    $valueToSanitize
                );

                $this->handleLogging(
                    $arrayToSanitize,
                    $nameToSanitize,
                    $initialValueToSanitize,
                    $valueToSanitize
                );
            }
        }

        return $arrayToSanitize;
    }

    /**
     * @param array  $rules
     * @param string $nameToSanitize
     *
     * @return array
     */
    private function getRulesForValue(array $rules, string $nameToSanitize)
    {
        $rulesForValue = $this->getSpecialRulesByName($rules, $nameToSanitize);

        if (!$rulesForValue) {
            $rulesForValue = $this->getCommonRulesByName($rules, $nameToSanitize);
        }

        if (!$rulesForValue) {
            $rulesForValue = $rules[Rules::DEFAULT_RULES_KEY];
        }

        return $rulesForValue;
    }

    /**
     * @return array
     */
    private function getSpecialRulesByName($rules, $nameToSanitize)
    {
        return isset($rules[$nameToSanitize]) ? $rules[$nameToSanitize] : null;
    }

    /**
     * @return mixed
     */
    private function getCommonRulesByName($rules, $nameToSanitize)
    {
        return
            (
                isset($rules[Rules::COMMON_RULES_KEY]) &&
                isset($rules[Rules::COMMON_RULES_KEY][$nameToSanitize])
            ) ? $rules[Rules::COMMON_RULES_KEY][$nameToSanitize] : null;
    }

    /**
     * @param array $rulesFromCurrentLevel
     * @param array $rulesForNextLevel
     *
     * @return array
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
     * @param array $rulesFromCurrentLevel
     * @param array $rulesForNextLevel
     *
     * @return array
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
     * @param array $rulesForValue
     * @param array $allRules
     * @param string $rulesKey
     *
     * @return array
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

    /**
     * @param array $arrayToSanitize
     * @param mixed $nameToSanitize
     * @param mixed $initialValueToSanitize
     * @param mixed $sanitizedValue
     */
    private function handleLogging(
        array $arrayToSanitize,
        $nameToSanitize,
        $initialValueToSanitize,
        $sanitizedValue
    ): void {
        if (!Factory::getConfiguration()->isLogMode()) {
            return;
        }

        $this->getLogger()->warning(
            self::MESSAGE_VALUE_HAS_CHANGED,
            [
                'Parameter Name:' => $nameToSanitize,
                'initialer Wert:' => $initialValueToSanitize,
                'Wert nach Bereinigung:' => $sanitizedValue,
                'komplettes Parameter Array' => $arrayToSanitize,
            ]
        );
    }

    /**
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        return Factory::getLogger(__CLASS__);
    }

    /**
     * @param array $arrayToSanitize
     * @param mixed $nameToSanitize
     * @param mixed $initialValueToSanitize
     * @param mixed $sanitizedValue
     *
     * @TODO Refactor, dont echo directly, do not change output
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function handleDebugging(
        array $arrayToSanitize,
        $nameToSanitize,
        $initialValueToSanitize,
        $sanitizedValue
    ): void {
        if (!DebugUtility::isDebugMode()) {
            return;
        }

        $this->echoDebug(
            [
                'Parameter Name:' => $nameToSanitize,
                'initialer Wert:' => $initialValueToSanitize,
                'Wert nach Bereinigung:' => $sanitizedValue,
                'komplettes Parameter Array' => $arrayToSanitize,
            ]
        );

        if (TYPO3_MODE == 'FE') {
            $GLOBALS['TYPO3_CONF_VARS']['FE']['compressionLevel'] = 0;
            ob_flush();
        }
    }

    /**
     * Just a wrapper for DebugUtility::debug.
     *
     * @param array $data
     */
    protected function echoDebug(array $data): void
    {
        DebugUtility::debug(
            $data,
            self::MESSAGE_VALUE_HAS_CHANGED
        );
    }
}
