<?php
defined('TYPO3_MODE') || die('Access denied.');

// $defaultRules = array(
// 	'default'	=> array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_ENCODED)
// );
$defaultRulesForFrontend = 
	unserialize('a:1:{s:7:"default";a:2:{i:0;i:513;i:1;i:514;}}');

tx_mksanitizedparameters_Rules::addRulesForFrontend($defaultRulesForFrontend);

// $rulesForBackend =  array(
// 	'default'	=> array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_ENCODED)
// );
$rulesForBackend = unserialize('a:1:{s:7:"default";a:2:{i:0;i:513;i:1;i:514;}}');
tx_mksanitizedparameters_Rules::addRulesForBackend($rulesForBackend);
