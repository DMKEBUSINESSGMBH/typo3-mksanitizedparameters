<?php

defined('TYPO3_MODE') || exit('Access denied.');

// $rulesForFrontend = [
//     'tx_form_formframework' => [
//         tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => [
//             // internal parameter that needs no sanitizing
//             '__state' => FILTER_UNSAFE_RAW,
//         ]
//     ],
// ];

tx_mksanitizedparameters_Rules::addRulesForFrontend(unserialize(
    'a:1:{s:21:"tx_form_formframework";a:1:{s:8:"__common";a:1:{s:7:"__state";i:516;}}}'
));
