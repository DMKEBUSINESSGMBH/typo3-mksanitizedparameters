<?php
defined('TYPO3_MODE') || die('Access denied.');

// the default config for common TYPO3 request parameters.
// add your own parameter rules in localconf.php similar to the
// config below or overwrite them. examples for the config possibilities  
// can be found in class.tx_mksanitizedparameters.php. You can also check
// the testcases in /tests to see how the classes work.
// NOTE: your config should be stored serialized for performance reasons.
// the config would be then something like:
// $rulesForFrontend = unserialize(HERE_COMES_YOU_SERIALIZED_ARRAY)
// tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForFrontend);
tx_rnbase::load('tx_mksanitizedparameters_Rules');

$rulesForCaretaker = array();
if(t3lib_extMgm::isLoaded('caretaker_instance')){
// 	$rulesForCaretaker = array(
// 		'st'		=> FILTER_SANITIZE_STRING,
// 		'd' 		=> FILTER_UNSAFE_RAW,
// 		's'
// 	);
	$rulesForCaretaker = 
		unserialize('a:3:{s:2:"st";i:513;s:1:"d";i:516;i:0;s:1:"s";}');
}

// $defaultRules = array(
// 	'default'	=> array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_ENCODED)
// );
$defaultRulesForFrontend = 
	unserialize('a:1:{s:7:"default";a:2:{i:0;i:513;i:1;i:514;}}');

$rulesForFrontend = 
	array_merge_recursive($defaultRulesForFrontend,$rulesForCaretaker);
tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForFrontend);

// $rulesForBackend =  array(
// 	'default'	=> array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_ENCODED)
// );
$rulesForBackend = unserialize('a:1:{s:7:"default";a:2:{i:0;i:513;i:1;i:514;}}');
tx_mksanitizedparameters_Rules::addRulesForBackend($rulesForBackend);