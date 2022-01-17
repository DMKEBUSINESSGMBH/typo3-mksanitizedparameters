<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mksanitizedparameters" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * Stores the given arrays to the DB so it can be checked
 * which parameters have which values.
 *
 * @deprecated use \DMK\MkSanitizedParameters\Monitor instead
 *
 * @author Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 *
 * @SuppressWarnings(PHPMD)
 */
class tx_mksanitizedparameters_StealthMode extends \DMK\MkSanitizedParameters\Monitor
{
    /**
     * Stores the given arrays to the DB so it can be checked
     * which parameters have which values.
     *
     * @param array $arraysToMonitor
     */
    public static function monitorArrays(array $arraysToMonitor)
    {
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
        $monitor = \DMK\MkSanitizedParameters\Factory::getMonitor();
        $monitor->monitorInput(
            \DMK\MkSanitizedParameters\Factory::createInput(
                \DMK\MkSanitizedParameters\Input\ArrayInput::class,
                ...[$arrayKey, $arrayValues]
            )
        );
    }
}
