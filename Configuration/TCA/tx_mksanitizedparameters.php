<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

return array(
    'ctrl' => array(
        'title'     => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters',
        'label'     => 'name',
        'label_alt'    => 'value',
        'label_alt_force' => 1,
        'default_sortby' => 'ORDER BY crdate',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,name,value,hash'
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => array(
            'label' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters.name',
            'exclude' => 0,
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '255',
            ),
            'readOnly' => 1
        ),
        'value' => array(
            'label' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters.value',
            'exclude' => 1,
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '3',
            ),
            'readOnly' => 1
        ),
        'hash' => array(
            'label' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters.hash',
            'exclude' => 1,
            'config' => array(
                'type' => 'text',
            ),
            'readOnly' => 1
        )
    ),
    'types' => array(
        '0' => array('showitem' => 'hidden;;1;;1-1-1, name, value, hash'
                    )
    ),
    'palettes' => array(
        '1' => array('showitem' => '')
    )
);
