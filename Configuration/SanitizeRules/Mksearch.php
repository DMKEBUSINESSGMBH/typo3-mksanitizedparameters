<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

// $rulesForMksearch
\DMK\MkSanitizedParameters\Rules::addRulesForFrontend(
    [
        'mksearch' => [
            // darum kümmert sich mksearch selbst
            'term' => FILTER_UNSAFE_RAW,
        ],
    ]
);
