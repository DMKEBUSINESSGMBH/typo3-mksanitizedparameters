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
 * Stores the given arrays to the DB so it can be checked
 * which parameters have which values.
 *
 * @author Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mksanitizedparameters_StealthMode
{
    private static $storagePid;
    private static $storageDbTableName = 'tx_mksanitizedparameters';

    /**
     * returns the db connection.
     *
     * @return Tx_Rnbase_Database_Connection
     */
    protected static function getDatabaseConnection()
    {
        return Tx_Rnbase_Database_Connection::getInstance();
    }

    /**
     * Stores the given arrays to the DB so it can be checked
     * which parameters have which values.
     *
     * @param array $arraysToMonitor
     */
    public static function monitorArrays(array $arraysToMonitor)
    {
        self::$storagePid = tx_rnbase_configurations::getExtensionCfgValue(
            'mksanitizedparameters',
            'stealthModeStoragePid'
        );

        foreach ($arraysToMonitor as $arrayKey => $arrayToMonitor) {
            self::monitorArray($arrayKey, $arrayToMonitor);
        }
    }

    /**
     * @param string $arrayKey
     * @param array  $arrayValues
     */
    public static function monitorArray($arrayKey, array $arrayValues)
    {
        if (empty($arrayValues) ||
            self::arrayWasAlreadyMonitored($arrayKey, $arrayValues)
        ) {
            return;
        }

        $dataToInsert = [
            'pid' => self::$storagePid,
            'name' => $arrayKey,
            'value' => self::getArrayAsStringOutput(
                $arrayValues
            ),
            'hash' => self::getHashByArrayToMonitor(
                $arrayKey,
                $arrayValues
            ),
            'crdate' => $GLOBALS['EXEC_TIME'],
        ];
        self::getDatabaseConnection()->doInsert(
            self::$storageDbTableName,
            $dataToInsert
        );
    }

    /**
     * @param string $arrayKey
     * @param array  $arrayToMonitor
     *
     * @return bool
     */
    private static function arrayWasAlreadyMonitored(
        $arrayKey,
        array $arrayToMonitor
    ) {
        $arrayHash = self::getHashByArrayToMonitor(
            $arrayKey,
            $arrayToMonitor
        );

        $where = 'hash = "'.$arrayHash.'"';

        $selectResult = self::getDatabaseConnection()->doSelect(
            '*',
            self::$storageDbTableName,
            [
                'where' => $where,
                'enablefieldsfe' => true,
            ]
        );

        return !empty($selectResult);
    }

    /**
     * @param array $array
     *
     * @return string
     */
    private static function getArrayAsStringOutput(array $array)
    {
        return var_export($array, true);
    }

    /**
     * @param string $arrayKey
     * @param array  $arrayValues
     *
     * @return string
     */
    private static function getHashByArrayToMonitor($arrayKey, array $arrayValues)
    {
        return md5($arrayKey.self::getArrayAsStringOutput($arrayValues));
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/class.tx_mksanitizedparameters_StealthMode.php']) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/class.tx_mksanitizedparameters_StealthMode.php'];
}
