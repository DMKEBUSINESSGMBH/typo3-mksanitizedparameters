<?php
/**
 * Extension Manager/Repository config file for ext "mklib".
 * @package tx_mktools
 * @subpackage tx_mktools_
 */

########################################################################
# Extension Manager/Repository config file for ext "mktools".
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'MK Sanitized Parameters',
	'description' => 'Sanitize $_REQUEST, $_POST and $_GET before the processing of TYPO3 in backend or frontend starts. Take a look into ext_localconf.php how to configure.',
	'category' => 'misc',
	'author' => 'das MedienKombinat GmbH',
	'author_email' => 'kontakt@das-medienkombinat.de',
	'author_company' => 'das Medienkombinat GmbH',
	'shy' => '',
	'dependencies' => 'rn_base', 
	'version' => '0.0.1',
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
			'rn_base'	=> ''
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);