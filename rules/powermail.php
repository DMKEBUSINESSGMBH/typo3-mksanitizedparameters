<?php
defined('TYPO3_MODE') || die('Access denied.');

//$rulesForFrontend = array(
// 	'tx_powermail_pi1' => array(
// 		// JSON String
// 		'__hmac' => FILTER_UNSAFE_RAW
// 	)
//);

$rulesForFrontend = 
	unserialize('a:1:{s:16:"tx_powermail_pi1";a:1:{s:6:"__hmac";i:516;}}');

tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForFrontend);
