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

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:mksanitizedparameters/locallang.xlf:tx_mksanitizedparameters',
        'label' => 'name',
        'label_alt' => 'value',
        'label_alt_force' => 1,
        'default_sortby' => 'ORDER BY crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,name,value,hash',
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'name' => [
            'label' => 'LLL:EXT:mksanitizedparameters/locallang.xlf:tx_mksanitizedparameters.name',
            'exclude' => 0,
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '255',
            ],
            'readOnly' => 1,
        ],
        'value' => [
            'label' => 'LLL:EXT:mksanitizedparameters/locallang.xlf:tx_mksanitizedparameters.value',
            'exclude' => 1,
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '3',
            ],
            'readOnly' => 1,
        ],
        'hash' => [
            'label' => 'LLL:EXT:mksanitizedparameters/locallang.xlf:tx_mksanitizedparameters.hash',
            'exclude' => 1,
            'config' => [
                'type' => 'text',
            ],
            'readOnly' => 1,
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'hidden;;1;;1-1-1, name, value, hash',
                    ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];
