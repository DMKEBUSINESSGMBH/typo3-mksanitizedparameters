<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

// $rulesForFrontend
\DMK\MkSanitizedParameters\Rules::addRulesForFrontend(
    [
        'tx_fluidrecommendation_pi1' => [
            'url' => FILTER_SANITIZE_URL,
            // JSON String
            '__hmac' => FILTER_UNSAFE_RAW,
            '__referrer' => [
                'extensionName' => FILTER_SANITIZE_STRING,
                'controllerName' => FILTER_SANITIZE_STRING,
                'actionName' => FILTER_SANITIZE_STRING,
            ],
            'recommendation' => [
                'receiverLastName' => FILTER_SANITIZE_STRING,
                'receiverMail' => FILTER_SANITIZE_EMAIL,
                'senderLastName' => FILTER_SANITIZE_STRING,
                'senderMail' => FILTER_SANITIZE_EMAIL,
                'message' => FILTER_SANITIZE_STRING,
                'url' => FILTER_SANITIZE_URL,
            ],
        ],
        'tx_fluid_recommendation' => [
            'success' => FILTER_UNSAFE_RAW,
        ],
    ]
);
