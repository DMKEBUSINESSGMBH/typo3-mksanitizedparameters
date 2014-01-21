<?php
defined('TYPO3_MODE') || die('Access denied.');

// the default rules for common TYPO3 request parameters.
// add your own parameter rules in localconf.php similar to the
// rules below or overwrite them. examples for the rules possibilities
// can be found in class.tx_mksanitizedparameters.php. You can also check
// the testcases in /tests to see how the classes work.
// NOTE: your rules should be stored serialized for performance reasons.
// the rules would be then something like:
// $rulesForFrontend = unserialize(HERE_COMES_YOU_SERIALIZED_ARRAY)
// tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForFrontend);
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_mksanitizedparameters_Rules');

require_once(t3lib_extMgm::extPath($_EXTKEY).'rules/default.php');

if(t3lib_extMgm::isLoaded('caretaker_instance')){
	require_once(t3lib_extMgm::extPath($_EXTKEY).'rules/caretaker_instance.php');
}

if(t3lib_extMgm::isLoaded('mksearch')){
	require_once(t3lib_extMgm::extPath($_EXTKEY).'rules/mksearch.php');
}

if(t3lib_extMgm::isLoaded('fluid_recommendation')){
	require_once(t3lib_extMgm::extPath($_EXTKEY).'rules/fluid_recommendation.php');
}

if(t3lib_extMgm::isLoaded('powermail')){
	require_once(t3lib_extMgm::extPath($_EXTKEY).'rules/powermail.php');
}