<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::allowTableOnStandardPages('tx_mksanitizedparameters');

$TCA['tx_mksanitizedparameters'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:mksanitizedparameters/locallang.xml:tx_mksanitizedparameters',
		'label'     => 'name',
		'label_alt'	=> 'value',
		'label_alt_force' => 1,
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca/tx_mksanitizedparameters.php'
	),
);