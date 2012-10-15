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
 * Stores the given arrays to the DB so it can be checked
 * which parameters have which values.
 * 
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <hannes.bochmann@das-mediekombinat.de>
 */
class tx_mksanitizedparameters_StealthMode {
	
	private static $storagePid;
	
	/**
	 * Stores the given arrays to the DB so it can be checked
 	 * which parameters have which values.
 	 * 
	 * @param array $arraysToMonitor
	 * 
	 * @return void
	 */
	public static function monitorArrays(array $arraysToMonitor) {
		self::loadTca();
		
		self::$storagePid = tx_rnbase_configurations::getExtensionCfgValue(
			'mksanitizedparameters', 'stealthModeStoragePid'
		);
		
		foreach ($arraysToMonitor as $arrayKey => $arrayToMonitor) {
			self::monitorArray($arrayKey, $arrayToMonitor);
		}
	}
	
	/**
	 * its possible that this script is used in an eID which
	 * causes no TCA to be available. We fix this!
	 * 
	 * @return void
	 */
	private function loadTca() {
		global $TYPO3_CONF_VARS, $TCA;
		if(empty($TCA['tx_mksanitizedparameters'])) {
			t3lib_div::makeInstance('tslib_fe',$TYPO3_CONF_VARS)->includeTCA(0);
			t3lib_div::loadTCA('tx_mksanitizedparameters');
		}
	}
	
	/**
	 * @param string $arrayKey
	 * @param array $arrayValues
	 * 
	 * @return void
	 */
	public static function monitorArray($arrayKey, array $arrayValues) {
		if(
			empty($arrayValues) || 
			self::arrayWasAlreadyMonitored($arrayKey,$arrayValues)
		) {
			return;
		} 
		
		$dataToInsert = array(
			'tx_mksanitizedparameters' => array(
				'NEW_123' => array(
					'pid' 	=> self::$storagePid,
					'name'  => $arrayKey,
					'value' => self::getArrayAsStringOutput($arrayValues),
					'hash'	=> self::getHashByArrayToMonitor($arrayKey, $arrayValues)
				)
			)
		);
		$tceMain = tx_rnbase_util_DB::getTCEmain($dataToInsert);
		$tceMain->process_datamap();
	}
	
	/**
	 * @param string $arrayKey
	 * @param array $arrayToMonitor
	 * 
	 * @return boolean
	 */
	private static function arrayWasAlreadyMonitored(
		$arrayKey, array $arrayToMonitor
	) {
		$arrayHash = self::getHashByArrayToMonitor(
			$arrayKey, $arrayToMonitor
		);
		
		$where = 'hash = "' . $arrayHash . '"';
		
		$selectResult = tx_rnbase_util_DB::doSelect(
			'*', 
			'tx_mksanitizedparameters', 
			array(
				'where' => $where,
				'enablefieldsfe' => true
			)
		);

		return !empty($selectResult);
	}
	
	/**
	 * @param array $array
	 * 
	 * @return string
	 */
	private function getArrayAsStringOutput(array $array) {
		return var_export($array,true);
	}
	
	/**
	 * @param string $arrayKey
	 * @param array $arrayValues
	 * 
	 * @return string
	 */
	private static function getHashByArrayToMonitor($arrayKey, array $arrayValues) {
		return md5($arrayKey.self::getArrayAsStringOutput($arrayValues));
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/class.tx_mksanitizedparameters_StealthMode.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/class.tx_mksanitizedparameters_StealthMode.php']);
}