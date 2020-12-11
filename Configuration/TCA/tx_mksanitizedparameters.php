<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters',
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
            'label' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters.name',
            'exclude' => 0,
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '255',
            ],
            'readOnly' => 1,
        ],
        'value' => [
            'label' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters.value',
            'exclude' => 1,
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '3',
            ],
            'readOnly' => 1,
        ],
        'hash' => [
            'label' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters.hash',
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
