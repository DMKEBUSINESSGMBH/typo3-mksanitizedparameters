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
 * Class to register and retrieve configuration for
 * the parameters in the system
 * 
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <hannes.bochmann@das-mediekombinat.de>
 */
class tx_mksanitizedparameters_Configuration {
	
	/**
	 * the default environment is Frontend
	 * 
	 * @return array
	 */
	public static function getParameterConfigurationForCurrentEnvironment() {
		switch (TYPO3_MODE) {
			case 'FE':
			default:
				$parameterRulesForCurrentEnvironment =
					tx_mksanitizedparameters_Configuration::getParameterConfigurationForFrontend();
				break;
			case 'BE':
				$parameterRulesForCurrentEnvironment =
					tx_mksanitizedparameters_Configuration::getParameterConfigurationForBackend();
				break;
		}
		
		return $parameterRulesForCurrentEnvironment;
	}
	
	/**
	 * @return array
	 */
	public static function getParameterConfigurationForFrontend() {
		return 
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mksanitizedparameters']['parameterRules']['FE'];
	}
	
	/**
	* @return array
	*/
	public static function getParameterConfigurationForBackend() {
		return
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mksanitizedparameters']['parameterRules']['BE'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/util/class.tx_mksanitizedparameters_Configuration.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/util/class.tx_mksanitizedparameters_Configuration.php']);
}