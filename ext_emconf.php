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
	'description' => 'Sanitize $_REQUEST, $_POST and $_GET before the processing of TYPO3 in backend or frontend starts. Take a look into ext_rules.php how to add your own rules or see which one exist.',
	'category' => 'misc',
	'author' => 'das MedienKombinat GmbH',
	'author_email' => 'kontakt@das-medienkombinat.de',
	'author_company' => 'das Medienkombinat GmbH',
	'shy' => '',
	'dependencies' => 'rn_base,mklib', 
	'version' => '0.2.3',
	'conflicts' => '',
	'priority' => 'top',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'constraints' => array(
		'depends' => array(
			'rn_base'	=> '',
			'mklib'	=> ''
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);