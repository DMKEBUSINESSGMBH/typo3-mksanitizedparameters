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
tx_rnbase::load('tx_mksanitizedparameters');

/**
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <hannes.bochmann@das-mediekombinat.de>
 */
class tx_mksanitizedparameters_hooks_PreprocessTypo3Requests {

	/**
	 * sanitize $_REQUEST, $_POST, $_GET before 
	 * Frontend/Backend Actions start.
	 * 
	 * @param array $parameters
	 * @param $parent
	 * 
	 * @return void
	 */
	public function sanitizeGlobalInputArrays(array $parameters, $parent) {
		$isStealthMode = tx_rnbase_configurations::getExtensionCfgValue(
			'mksanitizedparameters', 'stealthMode'
		);
		$arraysToSanitize = array(
			'$_REQUEST' => &$_REQUEST,
			'$_POST' => &$_POST,
			'$_GET' => &$_GET 
		);
		
		if($isStealthMode) {
			tx_rnbase::load('tx_mksanitizedparameters_StealthMode');
			tx_mksanitizedparameters_StealthMode::monitorArrays(
				$arraysToSanitize
			);
		} else {
			$typo3Mode = (TYPO3_MODE == 'BE')  ? TYPO3_MODE : 'FE';
			
			$parameterRules = 
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mksanitizedparameters']['parameterRules'][$typo3Mode];
	t3lib_div::debug(array(
		$arraysToSanitize
		),__METHOD__.' Line: '.__LINE__); // @TODO: remove me
			tx_mksanitizedparameters::sanitizeArraysByConfig(
				$arraysToSanitize, $parameterRules
			);
			t3lib_div::debug(array(
		$arraysToSanitize
		),__METHOD__.' Line: '.__LINE__); // @TODO: remove me
		exit;
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php']);
}