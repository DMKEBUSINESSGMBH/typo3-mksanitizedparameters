<?php
defined('TYPO3_MODE') || die('Access denied.');

// 	$rulesForCaretaker = array(
// 		'st'		=> FILTER_SANITIZE_STRING,
// 		'd' 		=> FILTER_UNSAFE_RAW,
// 		's'
// 	);
$rulesForCaretaker = 
	unserialize('a:3:{s:2:"st";i:513;s:1:"d";i:516;i:0;s:1:"s";}');

tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForCaretaker);