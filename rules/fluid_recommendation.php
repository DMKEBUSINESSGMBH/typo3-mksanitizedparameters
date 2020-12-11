<?php

defined('TYPO3_MODE') || exit('Access denied.');

// $rulesForFrontend = array(
//     'tx_fluidrecommendation_pi1' => array(
//         'url' => FILTER_SANITIZE_URL,
//         // JSON String
//         '__hmac' => FILTER_UNSAFE_RAW,
//         '__referrer' => array(
//             'extensionName' => FILTER_SANITIZE_STRING,
//             'controllerName' =>    FILTER_SANITIZE_STRING,
//             'actionName' =>    FILTER_SANITIZE_STRING,
//         ),
//         'recommendation' => array(
//             'receiverLastName' => FILTER_SANITIZE_STRING,
//             'receiverMail' => FILTER_SANITIZE_EMAIL,
//             'senderLastName' => FILTER_SANITIZE_STRING,
//             'senderMail' => FILTER_SANITIZE_EMAIL,
//             'message' => FILTER_SANITIZE_STRING,
//             'url' => FILTER_SANITIZE_URL
//         )
//     ),
//     'tx_fluid_recommendation' => array(
//         'success' => FILTER_UNSAFE_RAW,
//     ),
// );
$rulesForFrontend =
    unserialize('a:2:{s:26:"tx_fluidrecommendation_pi1";a:4:{s:3:"url";i:518;s:6:"__hmac";i:516;s:10:"__referrer";a:3:{s:13:"extensionName";i:513;s:14:"controllerName";i:513;s:10:"actionName";i:513;}s:14:"recommendation";a:6:{s:16:"receiverLastName";i:513;s:12:"receiverMail";i:517;s:14:"senderLastName";i:513;s:10:"senderMail";i:517;s:7:"message";i:513;s:3:"url";i:518;}}s:23:"tx_fluid_recommendation";a:1:{s:7:"success";i:516;}}');

tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForFrontend);
