<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

// $rulesForFrontend
\DMK\MkSanitizedParameters\Rules::addRulesForFrontend(
    [
        'tx_form_formframework' => [
            \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => [
                // internal parameter that needs no sanitizing
                '__state' => FILTER_UNSAFE_RAW,
            ],
        ],
    ]
);
