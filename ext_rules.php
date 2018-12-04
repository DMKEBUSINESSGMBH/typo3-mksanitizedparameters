<?php
defined('TYPO3_MODE') || die('Access denied.');

$_EXTKEY = 'mksanitizedparameters';

// the default rules for common TYPO3 request parameters.
// add your own parameter rules in localconf.php similar to the
// rules below or overwrite them. examples for the rules possibilities
// can be found in class.tx_mksanitizedparameters.php. You can also check
// the testcases in /tests to see how the classes work.
// NOTE: your rules should be stored serialized for performance reasons.
// the rules would be then something like:
// $rulesForFrontend = unserialize(HERE_COMES_YOU_SERIALIZED_ARRAY)
// tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForFrontend);

tx_rnbase::load('tx_mksanitizedparameters_Rules');

require_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'rules/default.php');

if (tx_rnbase_util_Extensions::isLoaded('caretaker_instance')) {
    require_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'rules/caretaker_instance.php');
}

if (tx_rnbase_util_Extensions::isLoaded('mksearch')) {
    require_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'rules/mksearch.php');
}

if (tx_rnbase_util_Extensions::isLoaded('fluid_recommendation')) {
    require_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'rules/fluid_recommendation.php');
}

if (tx_rnbase_util_Extensions::isLoaded('powermail')) {
    require_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'rules/powermail.php');
}

if (tx_rnbase_util_Extensions::isLoaded('sr_feuser_register')) {
    require_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'rules/sr_feuser_register.php');
}

if (tx_rnbase_util_Extensions::isLoaded('form')) {
    require_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'rules/form.php');
}
