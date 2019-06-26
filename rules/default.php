<?php

defined('TYPO3_MODE') || die('Access denied.');

// $rulesForFrontend = array(
//     tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY    => array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_MAGIC_QUOTES),
//     tx_mksanitizedparameters_Rules::COMMON_RULES_KEY    => array(
//         // Extbase request token für Formulare
//         // JSON String
//         '__hmac' => FILTER_UNSAFE_RAW,

//         // wird nur mkforms intern verwendet
//         // JSON String
//         'AMEOSFORMIDABLE_ADDPOSTVARS' => FILTER_UNSAFE_RAW,

//         'id' => FILTER_SANITIZE_STRING,
//         // pid kann eine kommaseparierte Liste sein
//         'pid' => FILTER_SANITIZE_STRING,
//         // uid sollten immer zahlen sein
//          'uid' => FILTER_SANITIZE_NUMBER_INT,
//         // JSON String
//         '__trustedProperties' => FILTER_UNSAFE_RAW,
//         //for extbase since https://typo3.org/teams/security/security-bulletins/typo3-core/typo3-core-sa-2016-013/
//         '@request' => FILTER_UNSAFE_RAW,
//         '@vendor' => FILTER_UNSAFE_RAW,
//         // Passwörter sollten alles enthalten dürfen. Das sollte vor dem schreiben in die
//         // DB ohenhin gehashed werden.
//         'password' => FILTER_UNSAFE_RAW,
//         'pass' => FILTER_UNSAFE_RAW
//     )
// );

$rulesForFrontend =
    unserialize('a:2:{s:9:"__default";a:2:{i:0;i:513;i:1;i:521;}s:8:"__common";a:10:{s:6:"__hmac";i:516;s:27:"AMEOSFORMIDABLE_ADDPOSTVARS";i:516;s:2:"id";i:513;s:3:"pid";i:513;s:3:"uid";i:519;s:19:"__trustedProperties";i:516;s:8:"@request";i:516;s:7:"@vendor";i:516;s:8:"password";i:516;s:4:"pass";i:516;}}');

tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForFrontend);

// $rulesForBackend =  array(
//     tx_mksanitizedparameters_Rules::COMMON_RULES_KEY    => array(
//         'id' => FILTER_SANITIZE_STRING,
//         'uid' => FILTER_SANITIZE_STRING,
//         'pid' => FILTER_SANITIZE_STRING,
//     )
// );

$rulesForBackend = unserialize('a:1:{s:8:"__common";a:3:{s:2:"id";i:513;s:3:"uid";i:513;s:3:"pid";i:513;}}');
tx_mksanitizedparameters_Rules::addRulesForBackend($rulesForBackend);
