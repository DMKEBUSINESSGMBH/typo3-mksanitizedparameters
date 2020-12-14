<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

// $rulesForCaretaker
\DMK\MkSanitizedParameters\Rules::addRulesForFrontend(
    [
        'st' => FILTER_SANITIZE_STRING,
        'd' => FILTER_UNSAFE_RAW,
        's' => FILTER_UNSAFE_RAW,
    ]
);
