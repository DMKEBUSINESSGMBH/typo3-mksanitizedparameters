<?php

defined('TYPO3_MODE') || die('Access denied.');

//     $rulesForCaretaker = array(
//         'st'        => FILTER_SANITIZE_STRING,
//         'd'         => FILTER_UNSAFE_RAW,
//         's'            => FILTER_UNSAFE_RAW
//     );
$rulesForCaretaker =
    unserialize('a:3:{s:2:"st";i:513;s:1:"d";i:516;s:1:"s";i:516;}');

tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForCaretaker);
