<?php
/**
 *
 *  Copyright notice
 *
 *  (c) 2012 das MedienKombinat GmbH <kontakt@das-medienkombinat.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * include required classes
 */
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

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
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <hannes.bochmann@das-mediekombinat.de>
 */
class tx_mksanitizedparameters {

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
	 * 	- special rules
	 *  - common rules
	 *  - default rules
	 *
	 * array(
	 *
	 * 	// special parameters configuration.
	 *  // will be used first
	 *  // NOTE: it is not possible to have a configuration for the value
	 *	// myParameterQualifier and for the sub parameters in myParameterQualifier.
	 *	// only on is possible! either myParameterQualifier or myParameterQualifier[...]
	 * 	'myParameterQualifier' => array(
	 * 		'uid' => FILTER_SANITIZE_NUMBER_INT
	 * 		'searchWord' => array(
	 * 			'filter' => array(
	 * 				FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
	 * 			),
	 * 			'flags'	=> FILTER_FLAG_ENCODE_AMP
	 * 		),
	 * 		'subArray' => array(
	 * 			//so all unconfigured parameters inside subArray will get
	 * 			//the following default sanitization
	 * 			'__default' 	=> FILTER_SANITIZE_NUMBER_INT
	 *
	 * 			//that's the way to call a custom filter!
	 * 			//the custom class is loaded through tx_rnbase::load().
	 * 			//If your custom class can't be loaded
	 * 			//this way, then please load the class yourself before setting the rules
	 * 			'someValue'	=> array(
	 *				'filter'    => FILTER_CALLBACK,
	 *             	'options' 	=> array(
	 *             		'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
	 *				)
	 *			)
	 * 		)
	 * 	)
	 *
	 * 	// common parameters configuration
	 *  // will be used if no special configuration found for a parameter name
	 *  // can be inside a special rule, too.
	 *  // will be handed down to subsequent levels. existing parameter name configurations
	 *  // in subsequent levels have a higher priority and will not be overwritten.
	 *  '__common' => array(
	 *  	// no matter at which position every parameter with the name someOtherValueToo
	 *  	// will be sanitized with the following configuration as long as there is no
	 *  	// special configuration
 	 *		someOtherValueToo => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)
	 * 	),
	 *  'myExt' => array(
	 *		// this will add the common rules for everything inside myExt
	 *		// so the cmmon config for otherValueToo is availabe, too
	 * 		'__common' => array(
 	 *			someOtherValueToo => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)
	 * 		),
	 *  ),
	 * 	'__common' => array(
 	 *		otherValueToo => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)
	 * 	),
	 *
	 *
	 * 	// default parameters configuration
	 *  // will be used if no special and no common configuration is found for a parameter name
	 *  // can be inside a special rule, too.
	 *  // will be handed down to subsequent levels if there is no default configuration
	 * 	'__default' => FILTER_SANITIZE_STRING
	 * 	//OR
	 * 	'__default' => array(
	 * 		'filter' => array(
	 * 			FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
	 * 		),
	 * 		'flags'	=> FILTER_FLAG_ENCODE_AMP
	 * 	)
	 *  //OR
	 * 	'__default' => array(
 	 *		FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
	 * 	),
	 *  //OR
	 * 	'myExt => array(
	 * 		// this will overwrite the default rules for everything inside myExt
	 * 		default' => array(
 	 *			FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
	 * 		),
	 * 	),
	 * '__default' => FILTER_SANITIZE_STRING
	 * )
	 *
	 * for the following array:
	 *
	 * array(
	 * 	'myParameterQualifier' => array(
	 * 		'uid' => 1
	 * 		'searchWord' => 'johndoe',
	 * 		'subArray' => array(
	 * 			'someOtherValue' => ...
	 * 			'someOtherValueToo' => ...
	 * 		),
	 * 		'someOtherValueToo' => ...
	 * 	)
	 * )
	 *
	 * results in following sanitizing:
	 *
	 * array(
	 * 	'myParameterQualifier' => array(
	 * 		'uid' => the special rule will be used
	 * 		'searchWord' => the special rule will be used,
	 * 		'subArray' => array(
	 * 			'someOtherValue' => the default rule will be used
	 * 			'someOtherValueToo' => the common rule will be used
	 * 		),
	 * 		'someOtherValueToo' => the common rule will be used
	 * 	)
	 * )
	 *
	 * Attention:
	 * Filter Configs with an array containing the key "filter" have higher priority
	 * than filter configs containing array of multiple filters.
	 * Single Filter Configs without array have highest priority.
	 * So when you have a common config
	 * 	'__common' => array(
	 * 		'commonValue' => array(
 	 *			FILTER_SANITIZE_NUMBER_INT,....
 	 *		)
	 * 	)
	 * that is overwritten in a lower level with
	 * '__common' => array(
	 *   	'commonValue' => array(
	 * 			'filter' => array(
	 * 				FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
	 * 			),
	 * 			'flags'	=> FILTER_FLAG_ENCODE_AMP
	 * 		)
	 * 	)
	 * and again is overwritten in a lower level with
	 * '__common' => array(
	 * 		'commonValue' => array(
 	 *			FILTER_SANITIZE_NUMBER_INT,....
	 * 		)
	 * )
	 * that config that is used in the last level is the following:
	 * '__common' => array(
	 *   	'commonValue' => array(
	 * 			'filter' => array(
	 * 				FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
	 * 			),
	 * 			'flags'	=> FILTER_FLAG_ENCODE_AMP
	 * 		)
	 * 	)
	 * So you would need to declare the last config as followed:
	 * '__common' => array(
	 * 		'commonValue' => array(
	 * 			'filter' => array(
	 * 				FILTER_SANITIZE_NUMBER_INT
	 * 			)
	 * 		)
	 * 	)
	 * //OR
	 * '__common' => array(
	 * 		'commonValue' => FILTER_SANITIZE_NUMBER_INT
	 * 	)
	 */
	public static function sanitizeArrayByRules(
		array $arrayToSanitize, array $rules
	) {
		if(empty($rules)) {
			return $arrayToSanitize;
		}

		foreach ($arrayToSanitize as $nameToSanitize => &$valueToSanitize) {
			$initialValueToSanitize = $valueToSanitize;

			$rulesForValue = self::getRulesForValue(
				$rules, $nameToSanitize
			);

			if(is_array($valueToSanitize)) {
				// so we have them on the next level, too
				$rulesForValue =
					self::injectDefaultRulesFromCurrentIntoNextLevelIfNotSet(
						$rules, (array) $rulesForValue
					);
				$rulesForValue =
					self::injectCommonRulesFromCurrentIntoNextLevelIfNotSet(
						$rules, (array) $rulesForValue
					);

				$valueToSanitize = self::sanitizeArrayByRules(
					$valueToSanitize, $rulesForValue
				);
			} elseif(!empty($rulesForValue)) {
				$valueToSanitize = self::sanitizeValueByRule(
					$valueToSanitize,$rulesForValue
				);
			}

			if(self::valueToSanitizeHasChanged($initialValueToSanitize, $valueToSanitize)) {
				self::handleDebugging(
					$arrayToSanitize, $nameToSanitize, $initialValueToSanitize, $valueToSanitize
				);

				self::handleLogging(
					$arrayToSanitize, $nameToSanitize, $initialValueToSanitize, $valueToSanitize
				);
			}
		}

		return $arrayToSanitize;
	}

	/**
	 * @param mixed $rules
	 * @param string $nameToSanitize
	 *
	 * @return mixed
	 */
	private static function getRulesForValue($rules, $nameToSanitize) {
		if(!$rulesForValue = self::getSpecialRulesByName($rules, $nameToSanitize)) {
			$rulesForValue = self::getCommonRulesByName($rules, $nameToSanitize);
		}

		if(!$rulesForValue) {
			$rulesForValue = $rules[tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY];
		}

		return $rulesForValue;
	}

	/**
	 * @return mixed
	 */
	private static function getSpecialRulesByName($rules, $nameToSanitize) {
		return isset($rules[$nameToSanitize]) ? $rules[$nameToSanitize] : null;
	}

	/**
	 * @return mixed
	 */
	private static function getCommonRulesByName($rules, $nameToSanitize) {
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
	private static function injectDefaultRulesFromCurrentIntoNextLevelIfNotSet(
		array $rulesFromCurrentLevel, array $rulesForNextLevel
	) {
		$rulesForNextLevel = self::injectRulesByKey(
			(array) $rulesForNextLevel, $rulesFromCurrentLevel,
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
	private static function injectCommonRulesFromCurrentIntoNextLevelIfNotSet(
		array $rulesFromCurrentLevel, array $rulesForNextLevel
	) {
		$rulesForNextLevel = self::injectRulesByKey(
			(array) $rulesForNextLevel, $rulesFromCurrentLevel,
			tx_mksanitizedparameters_Rules::COMMON_RULES_KEY
		);

		$rulesForNextLevel[tx_mksanitizedparameters_Rules::COMMON_RULES_KEY] =
			t3lib_div::array_merge_recursive_overrule(
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
	private static function injectRulesByKey(
		array $rulesForValue, $allRules, $rulesKey
	) {
		if(!array_key_exists($rulesKey, $rulesForValue)) {
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
	private static function sanitizeValueByRule($valueToSanitize, $rule) {
		$valueToSanitize = trim($valueToSanitize);

		if(!is_array($rule)) {
			return filter_var($valueToSanitize,$rule);
		} else {
			return self::sanitizeValueByFilterConfig($valueToSanitize,$rule);
		}
	}

	/**
	 * @param mixed $valueToSanitize
	 * @param array $filterConfig
	 *
	 * @return mixed
	 */
	private static function sanitizeValueByFilterConfig(
		$valueToSanitize, array $filterConfig
	) {
		if(isset($filterConfig['filter'])) {
			$filters = $filterConfig['filter'];
			unset($filterConfig['filter']);
			$filters = !is_array($filters) ? array($filters) : $filters;
		} else {
			$filters = $filterConfig;
		}

		self::loadCustomFilterCallbackClass($filterConfig);

		foreach ($filters as $filter) {
			$valueToSanitize =
				filter_var($valueToSanitize, intval($filter), $filterConfig);
		}

		return $valueToSanitize;
	}

	/**
	 * @param array $filterConfig
	 *
	 * @return void
	 */
	private static function loadCustomFilterCallbackClass(array $filterConfig) {
		if(
			isset($filterConfig['options'][0]) ||
			is_string($filterConfig['options'][0])
		){
			try {
				tx_rnbase::load($filterConfig['options'][0]);
			} catch (Exception $e) {
			}
		}
	}

	/**
	 * @param mixed $initialValueToSanitize
	 * @param mixed $valueToSanitize
	 *
	 * @return boolean
	 */
	private static function valueToSanitizeHasChanged($initialValueToSanitize, $valueToSanitize) {
		return $initialValueToSanitize != $valueToSanitize;
	}

	/**
	 * @param array $arrayToSanitize
	 * @param mixed $nameToSanitize
	 * @param mixed $initialValueToSanitize
	 * @param mixed $sanitizedValue
	 *
	 * @return void
	 */
	private static function handleLogging(
		array $arrayToSanitize, $nameToSanitize, $initialValueToSanitize, $sanitizedValue
	) {
		$isLogMode = tx_rnbase_configurations::getExtensionCfgValue(
			'mksanitizedparameters', 'logMode'
		);

		if(!$isLogMode){
			return;
		}

		$logger = static::getLogger();
		$logger::warn(
			self::MESSAGE_VALUE_HAS_CHANGED,
			'mksanitizedparameters',
			array(
				'Parameter Name:'				=> $nameToSanitize,
				'initialer Wert:' 				=> $initialValueToSanitize,
				'Wert nach Bereinigung:'		=> $sanitizedValue,
				'komplettes Parameter Array'	=> $arrayToSanitize
			)
		);
	}

	/**
	 * @return tx_rnbase_util_Logger
	 */
	protected static function getLogger() {
		tx_rnbase::load('tx_rnbase_util_Logger');
		return tx_rnbase_util_Logger;
	}

	/**
	 * @param array $arrayToSanitize
	 * @param mixed $nameToSanitize
	 * @param mixed $initialValueToSanitize
	 * @param mixed $sanitizedValue
	 *
	 * @return void
	 */
	private static function handleDebugging(
		array $arrayToSanitize, $nameToSanitize, $initialValueToSanitize, $sanitizedValue
	) {
		if(!static::getDebugMode()){
			return;
		}

		if(TYPO3_MODE == 'FE') {
			ob_start();//da wir eine Ausgabe wollen bevor TYPO3 die FE Ausgabe startet
		}

		$debugger = static::getDebugger();
		$debugger::debug(
			array(
				array(
					'Parameter Name:'				=> $nameToSanitize,
					'initialer Wert:' 				=> $initialValueToSanitize,
					'Wert nach Bereinigung:'		=> $sanitizedValue,
					'komplettes Parameter Array'	=> $arrayToSanitize
				)
			),
			self::MESSAGE_VALUE_HAS_CHANGED
		);
	}


	/**
	 * @return boolean
	 */
	protected static function getDebugMode() {
		$debugModeByExtensionConfiguration = tx_rnbase_configurations::getExtensionCfgValue(
			'mksanitizedparameters', 'debugMode'
		);

		return
			$debugModeByExtensionConfiguration ||
			(defined('TYPO3_ERRORHANDLER_MODE') && TYPO3_ERRORHANDLER_MODE == 'debug')
		;
	}

	/**
	 * @return tx_rnbase_util_Debug
	 */
	protected static function getDebugger() {
		tx_rnbase::load('tx_rnbase_util_Debug');
		return tx_rnbase_util_Debug;
	}

	/**
	 * @param array $arraysToSanitize
	 * @param array $rules
	 *
	 * @return void
	 */
	public static function sanitizeArraysByRules(
		array &$arraysToSanitize, array $rules
	) {
		foreach ($arraysToSanitize as $arrayName => &$arrayToSanitize) {
			$arrayToSanitize = self::sanitizeArrayByRules(
				$arrayToSanitize, $rules
			);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/class.tx_mksanitizedparameters.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/class.tx_mksanitizedparameters.php']);
}