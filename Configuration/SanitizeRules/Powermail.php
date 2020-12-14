<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

// $rulesForFrontendVersion1
\DMK\MkSanitizedParameters\Rules::addRulesForFrontend(
    [
        'tx_powermail_pi1' => [
            'url' => FILTER_SANITIZE_URL,
            // JSON String
            '__hmac' => FILTER_UNSAFE_RAW,
            '__trustedProperties' => FILTER_UNSAFE_RAW,
            '__referrer' => [
                'extensionName' => FILTER_SANITIZE_STRING,
                'controllerName' => FILTER_SANITIZE_STRING,
                'actionName' => FILTER_SANITIZE_STRING,
            ],
            'form' => FILTER_SANITIZE_NUMBER_INT,
            'field' => [
                \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => FILTER_UNSAFE_RAW,
            ],
        ],
    ]
);
