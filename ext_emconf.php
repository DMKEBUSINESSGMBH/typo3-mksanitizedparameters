<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "mksanitizedparameters".
 *
 * Auto generated 17-09-2014 18:11
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'MK Sanitized Parameters',
	'description' => 'Sanitize $_REQUEST, $_POST and $_GET before the processing of TYPO3 in backend or frontend starts. Take a look into the documentation how to add your own rules or see which one exist.',
	'category' => 'misc',
	'author' => 'Hannes Bochmann',
	'author_email' => 'dev@dmk-ebusiness.de',
	'author_company' => 'DMK E-BUSINESS GmbH',
	'shy' => '',
	'dependencies' => 'rn_base',
	'version' => '1.0.3',
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
			'rn_base' => '',
			'typo3' => '4.5.0-6.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'mklib' => '',
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:39:{s:34:"class.tx_mksanitizedparameters.php";s:4:"54db";s:40:"class.tx_mksanitizedparameters_Rules.php";s:4:"7d16";s:46:"class.tx_mksanitizedparameters_StealthMode.php";s:4:"7ce9";s:21:"ext_conf_template.txt";s:4:"78b3";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"356e";s:13:"ext_rules.php";s:4:"15ac";s:14:"ext_tables.php";s:4:"40b7";s:14:"ext_tables.sql";s:4:"40ef";s:13:"locallang.xml";s:4:"d037";s:26:"Documentation/Includes.txt";s:4:"ef74";s:23:"Documentation/Index.rst";s:4:"8d5c";s:26:"Documentation/Settings.yml";s:4:"2775";s:36:"Documentation/Introduction/Index.rst";s:4:"662d";s:35:"Documentation/Rules/DefineRules.rst";s:4:"6ab9";s:37:"Documentation/Rules/ExistingRules.rst";s:4:"d9ba";s:29:"Documentation/Rules/Index.rst";s:4:"9579";s:35:"Documentation/UsersManual/Index.rst";s:4:"ac26";s:70:"hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php";s:4:"67f3";s:64:"interface/class.tx_mksanitizedparameters_interface_Sanitizer.php";s:4:"0e75";s:28:"rules/caretaker_instance.php";s:4:"7229";s:17:"rules/default.php";s:4:"397e";s:30:"rules/fluid_recommendation.php";s:4:"a515";s:18:"rules/mksearch.php";s:4:"c12c";s:19:"rules/powermail.php";s:4:"7868";s:28:"rules/sr_feuser_register.php";s:4:"ce40";s:60:"sanitizer/class.tx_mksanitizedparameters_sanitizer_Alnum.php";s:4:"13b3";s:60:"sanitizer/class.tx_mksanitizedparameters_sanitizer_Alpha.php";s:4:"ca0a";s:18:"tca/ext_tables.php";s:4:"07bf";s:32:"tca/tx_mksanitizedparameters.php";s:4:"e2f9";s:55:"tests/class.tx_mksanitizedparameters_Rules_testcase.php";s:4:"07f4";s:49:"tests/class.tx_mksanitizedparameters_testcase.php";s:4:"48cf";s:17:"tests/phpunit.xml";s:4:"860d";s:76:"tests/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php";s:4:"8868";s:85:"tests/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests_testcase.php";s:4:"b266";s:75:"tests/sanitizer/class.tx_mksanitizedparameters_sanitizer_Alnum_testcase.php";s:4:"a9e2";s:75:"tests/sanitizer/class.tx_mksanitizedparameters_sanitizer_Alpha_testcase.php";s:4:"c766";s:77:"tests/util/class.tx_mksanitizedparameters_util_RegularExpression_testcase.php";s:4:"021a";s:62:"util/class.tx_mksanitizedparameters_util_RegularExpression.php";s:4:"0be4";}',
);

?>