<?php
/**
 *  Copyright notice.
 *
 *  (c) 2012 DMK E-Business GmbH <dev@dmk-ebusiness.de>
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
 * @author Hannes Bochmann <dev@dmk-ebusiness.de>
 */
class tx_mksanitizedparameters_hooks_PreprocessTypo3Requests
{
    /**
     * sanitize $_REQUEST, $_POST, $_GET before
     * Frontend/Backend Actions start.
     */
    public function sanitizeGlobalInputArrays()
    {
        $isStealthMode = tx_rnbase_configurations::getExtensionCfgValue(
            'mksanitizedparameters',
            'stealthMode'
        );
        $arraysToSanitize = array(
            '$_POST' => &$_POST,
            '$_GET' => &$_GET,
        );

        if ($isStealthMode) {
            tx_mksanitizedparameters_StealthMode::monitorArrays(
                $arraysToSanitize
            );
        } else {
            $mksanitizedparametersMainClass = $this->getMksanitizedparametersMainClass();
            $mksanitizedparametersMainClass->sanitizeArraysByRules(
                $arraysToSanitize,
                tx_mksanitizedparameters_Rules::getRulesForCurrentEnvironment()
            );
        }
    }

    /**
     * wird in tests/hooks/class.ux_tx_mksanitizedparameters.php
     * überschrieben damit debug mode abgeschaltet werden kann.
     *
     * @return tx_mksanitizedparameters
     */
    protected function getMksanitizedparametersMainClass()
    {
        /** @var tx_mksanitizedparameters $class */
         $class = tx_rnbase::makeInstance('tx_mksanitizedparameters');

         return $class;
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php']) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php'];
}
