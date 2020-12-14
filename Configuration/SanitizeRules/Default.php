<?php

defined('TYPO3_MODE') || exit('Access denied.');

// $rulesForFrontend
\DMK\MkSanitizedParameters\Rules::addRulesForFrontend(
    [
        \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING, FILTER_SANITIZE_MAGIC_QUOTES],
        \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => [
            // Extbase request token für Formulare
            // JSON String
            '__hmac' => FILTER_UNSAFE_RAW,
            // wird nur mkforms intern verwendet
            // JSON String
            'AMEOSFORMIDABLE_ADDPOSTVARS' => FILTER_UNSAFE_RAW,
            'id' => FILTER_SANITIZE_STRING,
            // pid kann eine kommaseparierte Liste sein
            'pid' => FILTER_SANITIZE_STRING,
            // uid sollten immer zahlen sein
            'uid' => FILTER_SANITIZE_NUMBER_INT,
            // JSON String
            '__trustedProperties' => FILTER_UNSAFE_RAW,
            //for extbase since https://typo3.org/teams/security/security-bulletins/typo3-core/typo3-core-sa-2016-013/
            '@request' => FILTER_UNSAFE_RAW,
            '@vendor' => FILTER_UNSAFE_RAW,
            // Passwörter sollten alles enthalten dürfen. Das sollte vor dem schreiben in die
            // DB ohenhin gehashed werden.
            'password' => FILTER_UNSAFE_RAW,
            'pass' => FILTER_UNSAFE_RAW,
        ],
    ]
);

// $rulesForBackend
\DMK\MkSanitizedParameters\Rules::addRulesForBackend(
    [
        \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => [
            'id' => FILTER_SANITIZE_STRING,
            'uid' => FILTER_SANITIZE_STRING,
            'pid' => FILTER_SANITIZE_STRING,
        ],
    ]
);
