<?php
defined('TYPO3_MODE') || die('Access denied.');

// $defaultRules = array(
// 	'default'	=> array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_MAGIC_QUOTES)
// );
$defaultRulesForFrontend = 
	unserialize('a:1:{s:7:"default";a:2:{i:0;i:513;i:1;i:521;}}');

tx_mksanitizedparameters_Rules::addRulesForFrontend($defaultRulesForFrontend);

// $rulesForBackend =  array(
// 	'default'	=> array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_MAGIC_QUOTES)
// );
$rulesForBackend = unserialize('a:1:{s:7:"default";a:2:{i:0;i:513;i:1;i:521;}}');
tx_mksanitizedparameters_Rules::addRulesForBackend($rulesForBackend);
