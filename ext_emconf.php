<?php
/**
 * Extension Manager/Repository config file for ext "mksanitizedparameters".
 * @package TYPO3
 * @subpackage mksanitizedparameters
 */

########################################################################
# Extension Manager/Repository config file for ext "mksanitizedparameters".
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Sanitize input parameters system wide',
	'description' => 'Sanitize $_REQUEST, $_POST and $_GET before the processing of TYPO3 in backend or frontend starts. Take a look into the documentation how to add your own rules or see which one exist.',
	'category' => 'misc',
	'author' => 'Hannes Bochmann',
	'author_email' => 'dev@dmk-ebusiness.de',
	'author_company' => 'DMK E-Business GmbH',
	'shy' => '',
	'dependencies' => 'rn_base',
	'version' => '0.3.11',
	'conflicts' => '',
	'priority' => 'top',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'constraints' => array(
		'depends' => array(
			'rn_base'	=> '',
			'typo3' => '4.5.0-6.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'mklib' => ''
		),
	),
);

