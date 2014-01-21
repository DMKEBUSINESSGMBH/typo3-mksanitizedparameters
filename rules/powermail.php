<?php
defined('TYPO3_MODE') || die('Access denied.');

$rulesForFrontend = array(
	'tx_powermail_pi1' => array(
		'url' => FILTER_SANITIZE_URL,
		// JSON String
		'__hmac' => FILTER_UNSAFE_RAW,
		'__referrer' => array(
			'extensionName' => FILTER_SANITIZE_STRING,
			'controllerName' =>	FILTER_SANITIZE_STRING,
			'actionName' =>	FILTER_SANITIZE_STRING,
		),
		'form' => FILTER_SANITIZE_NUMBER_INT,
		'field' => array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> FILTER_UNSAFE_RAW
		)
	),
);

$rulesForFrontend =
	unserialize('a:1:{s:16:"tx_powermail_pi1";a:5:{s:3:"url";i:518;s:6:"__hmac";i:516;s:10:"__referrer";a:3:{s:13:"extensionName";i:513;s:14:"controllerName";i:513;s:10:"actionName";i:513;}s:4:"form";i:519;s:5:"field";a:1:{s:9:"__default";i:516;}}}');

tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForFrontend);