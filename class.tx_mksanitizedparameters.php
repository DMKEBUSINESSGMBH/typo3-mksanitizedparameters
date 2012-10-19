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
 * multi dimensional arrays, default values for unconfigured
 * parameters and multiple filters per value.
 * 
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <hannes.bochmann@das-mediekombinat.de>
 */
class tx_mksanitizedparameters {
	
	/**
	 * @param array $arrayToSanitize
	 * @param array $rules
	 * 
	 * @return array
	 * 
	 * Sample rules:
	 * 
	 * array(
	 * 
	 * 	//all unconfigured parameters will be sanitized
	 *  //with the default value 
	 * 	'default' => FILTER_SANITIZE_STRING
	 * 	//OR
	 * 	'default' => array(
	 * 		'filter' => array(
	 * 			FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES	
	 * 		),
	 * 		'flags'	=> FILTER_FLAG_ENCODE_AMP
	 * 	)
	 *  //OR
	 * 	'default' => array(
 	 *		FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES	
	 * 	), 
	 * 
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
	 * 			'default' 	=> FILTER_SANITIZE_NUMBER_INT
	 * 
	 * 			//that's the way to call a custom filter!
	 * 			'someValue'	=> array(
	 *				'filter'    => FILTER_CALLBACK,
	 *             	'options' 	=> array(
	 *             		'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
	 *				)
	 *			)
	 * 		)
	 * 	)
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
	 * 		)
	 * 	)
	 * )
	 */
	public static function sanitizeArrayByRules(
		array $arrayToSanitize, array $rules
	) {
		if(empty($rules)) {
			return $arrayToSanitize;
		}
			
		foreach ($arrayToSanitize as $nameToSanitize => &$valueToSanitize) {
			$rulesForValue = self::getRulesForValue(
				$rules, $nameToSanitize
			);
				
			if(is_array($valueToSanitize)) {
				$rulesForValue = self::injectDefaultRulesIfNeccessary(
					(array) $rulesForValue, $rules['default']
				);
				
				$valueToSanitize = self::sanitizeArrayByRules(
					$valueToSanitize, $rulesForValue
				);
			} elseif(!empty($rulesForValue)) {
				$valueToSanitize = self::sanitizeValueByRule(
					$valueToSanitize,$rulesForValue
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
		$rulesForValue = !empty($rules[$nameToSanitize]) ?
			$rules[$nameToSanitize] : $rules['default'];
		
		return $rulesForValue;
	}
	
	/**
	 * @param array $rules
	 * @param mixed $defaultRules
	 * 
	 * @return array
	 */
	private static function injectDefaultRulesIfNeccessary(
		array $rules, $defaultRules
	) {
		if(!array_key_exists('default', $rules)) {
			$rules['default'] = $defaultRules;
		}
		
		return $rules;
	}
	
	/**
	 * @param mixed $valueToSanitize
	 * @param mixed $rules
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
		
		foreach ($filters as $filter) {
			$valueToSanitize = 
				filter_var($valueToSanitize,$filter,$filterConfig);
		}
		
		return $valueToSanitize;
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