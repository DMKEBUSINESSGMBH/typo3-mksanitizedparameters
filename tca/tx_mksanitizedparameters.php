<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_mksanitizedparameters'] = array (
	'ctrl' => $TCA['tx_mksanitizedparameters']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,value,hash'
	),
	'feInterface' => $TCA['tx_mksanitizedparameters']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'__default' => '0'
			)
		),
		'name' => array(
			'label' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters.name',
			'exclude' => 0,
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max' => '255',
			),
			'readOnly' => 1
		),
		'value' => array (
			'label' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters.value',
			'exclude' => 1,
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '3',
			),
			'readOnly' => 1
		),
		'hash' => array(
			'label' => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters.hash',
			'exclude' => 1,
			'config' => Array (
				'type' => 'text',
			),
			'readOnly' => 1
		)
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, value, hash'
					)
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);