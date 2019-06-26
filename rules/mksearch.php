<?php

defined('TYPO3_MODE') || die('Access denied.');

//$rulesForMksearch = array(
//    'mksearch'        => array(
//         // darum kümmert sich mksearch selbst
//         'term' => FILTER_UNSAFE_RAW
//     ),
//);
$rulesForMksearch =
    unserialize('a:1:{s:8:"mksearch";a:1:{s:4:"term";i:516;}}');

tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForMksearch);
