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
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mksanitizedparameters
{
    /**
     * @var string
     */
    const MESSAGE_VALUE_HAS_CHANGED = 'Ein Wert wurde von mksanitizedparameters verändert!';

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
     *                  'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
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
    public function sanitizeArrayByRules(
        array $arrayToSanitize,
        array $rules
    ) {
        if (empty($rules)) {
            return $arrayToSanitize;
        }

        foreach ($arrayToSanitize as $nameToSanitize => &$valueToSanitize) {
            $initialValueToSanitize = $valueToSanitize;

            $rulesForValue = $this->getRulesForValue(
                $rules,
                $nameToSanitize
            );

            if (is_array($valueToSanitize)) {
                // so we have them on the next level, too
                $rulesForValue =
                    $this->injectDefaultRulesFromCurrentIntoNextLevelIfNotSet(
                        $rules,
                        (array) $rulesForValue
                    );
                $rulesForValue =
                    $this->injectCommonRulesFromCurrentIntoNextLevelIfNotSet(
                        $rules,
                        (array) $rulesForValue
                    );

                $valueToSanitize = $this->sanitizeArrayByRules(
                    $valueToSanitize,
                    $rulesForValue
                );
            } elseif (!empty($rulesForValue)) {
                $valueToSanitize = $this->sanitizeValueByRule(
                    $valueToSanitize,
                    $rulesForValue
                );
            }

            if ($this->valueToSanitizeHasChanged($initialValueToSanitize, $valueToSanitize)) {
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
     * @param mixed  $rules
     * @param string $nameToSanitize
     *
     * @return mixed
     */
    private function getRulesForValue($rules, $nameToSanitize)
    {
        if (!$rulesForValue = $this->getSpecialRulesByName($rules, $nameToSanitize)) {
            $rulesForValue = $this->getCommonRulesByName($rules, $nameToSanitize);
        }

        if (!$rulesForValue) {
            $rulesForValue = $rules[tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY];
        }

        return $rulesForValue;
    }

    /**
     * @return mixed
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
                isset($rules[tx_mksanitizedparameters_Rules::COMMON_RULES_KEY]) &&
                isset($rules[tx_mksanitizedparameters_Rules::COMMON_RULES_KEY][$nameToSanitize])
            ) ? $rules[tx_mksanitizedparameters_Rules::COMMON_RULES_KEY][$nameToSanitize] : null;
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
    ) {
        $rulesForNextLevel = $this->injectRulesByKey(
            (array) $rulesForNextLevel,
            $rulesFromCurrentLevel,
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY
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
    ) {
        $rulesForNextLevel = $this->injectRulesByKey(
            (array) $rulesForNextLevel,
            $rulesFromCurrentLevel,
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY
        );

        $rulesForNextLevel[tx_mksanitizedparameters_Rules::COMMON_RULES_KEY] =
            tx_rnbase_util_Arrays::mergeRecursiveWithOverrule(
                (array) $rulesFromCurrentLevel[tx_mksanitizedparameters_Rules::COMMON_RULES_KEY],
                (array) $rulesForNextLevel[tx_mksanitizedparameters_Rules::COMMON_RULES_KEY]
            );

        return $rulesForNextLevel;
    }

    /**
     * @param array $rules
     * @param mixed $defaultRules
     *
     * @return array
     */
    private function injectRulesByKey(
        array $rulesForValue,
        $allRules,
        $rulesKey
    ) {
        if (!array_key_exists($rulesKey, $rulesForValue)) {
            $rulesForValue[$rulesKey] = $allRules[$rulesKey];
        }

        return $rulesForValue;
    }

    /**
     * @param mixed $valueToSanitize
     * @param mixed $rule
     *
     * @return mixed
     */
    private function sanitizeValueByRule($valueToSanitize, $rule)
    {
        if (!is_array($rule)) {
            return filter_var($valueToSanitize, $rule);
        } else {
            return $this->sanitizeValueByFilterConfig($valueToSanitize, $rule);
        }
    }

    /**
     * @param mixed $valueToSanitize
     * @param array $filterConfig
     *
     * @return mixed
     */
    private function sanitizeValueByFilterConfig(
        $valueToSanitize,
        array $filterConfig
    ) {
        if (isset($filterConfig['filter'])) {
            $filters = $filterConfig['filter'];
            unset($filterConfig['filter']);
            $filters = !is_array($filters) ? [$filters] : $filters;
        } else {
            $filters = $filterConfig;
        }

        foreach ($filters as $filter) {
            $valueToSanitize =
                filter_var($valueToSanitize, intval($filter), $filterConfig);
        }

        return $valueToSanitize;
    }

    /**
     * @param mixed $initialValueToSanitize
     * @param mixed $valueToSanitize
     *
     * @return bool
     */
    private function valueToSanitizeHasChanged($initialValueToSanitize, $valueToSanitize)
    {
        return $initialValueToSanitize != $valueToSanitize;
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
    ) {
        $isLogMode = tx_rnbase_configurations::getExtensionCfgValue(
            'mksanitizedparameters',
            'logMode'
        );

        if (!$isLogMode) {
            return;
        }

        // wir rufen die Methode mit call_user_func_array auf, da sie
        // statisch ist, womit wir diese nicht mocken könnten
        call_user_func_array(
            [$this->getLogger(), 'warn'],
            [
                self::MESSAGE_VALUE_HAS_CHANGED,
                'mksanitizedparameters',
                [
                    'Parameter Name:' => $nameToSanitize,
                    'initialer Wert:' => $initialValueToSanitize,
                    'Wert nach Bereinigung:' => $sanitizedValue,
                    'komplettes Parameter Array' => $arrayToSanitize,
                ],
            ]
        );
    }

    /**
     * @return tx_rnbase_util_Logger
     */
    protected function getLogger()
    {
        return 'tx_rnbase_util_Logger';
    }

    /**
     * @param array $arrayToSanitize
     * @param mixed $nameToSanitize
     * @param mixed $initialValueToSanitize
     * @param mixed $sanitizedValue
     */
    private function handleDebugging(
        array $arrayToSanitize,
        $nameToSanitize,
        $initialValueToSanitize,
        $sanitizedValue
    ) {
        if (!$this->getDebugMode()) {
            return;
        }

        // wir rufen die Methode mit call_user_func_array auf, da sie
        // statisch ist, womit wir diese nicht mocken könnten
        call_user_func_array(
            [$this->getDebugger(), 'debug'],
            [
                [
                    [
                        'Parameter Name:' => $nameToSanitize,
                        'initialer Wert:' => $initialValueToSanitize,
                        'Wert nach Bereinigung:' => $sanitizedValue,
                        'komplettes Parameter Array' => $arrayToSanitize,
                    ],
                ],
                self::MESSAGE_VALUE_HAS_CHANGED,
            ]
        );

        if (TYPO3_MODE == 'FE') {
            $GLOBALS['TYPO3_CONF_VARS']['FE']['compressionLevel'] = 0;
            ob_flush();
        }
    }

    /**
     * @return bool
     */
    protected function getDebugMode()
    {
        $debugModeByExtensionConfiguration = tx_rnbase_configurations::getExtensionCfgValue(
            'mksanitizedparameters',
            'debugMode'
        );

        return $debugModeByExtensionConfiguration || tx_rnbase_util_Network::isDevelopmentIp();
    }

    /**
     * @return tx_rnbase_util_Debug
     */
    protected function getDebugger()
    {
        return 'tx_rnbase_util_Debug';
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
