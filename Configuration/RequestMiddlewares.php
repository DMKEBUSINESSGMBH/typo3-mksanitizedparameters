<?php

return [
    'frontend' => [
        'dmk/mksanitizedparameters/global-input-sanitizer' => [
            'target' => \DMK\MkSanitizedParameters\Middleware\GlobalInputSanitizerMiddleware::class,
            'after' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
            'before' => [
                'typo3/cms-frontend/eid',
            ],
        ],
    ],
    'backend' => [
        'dmk/mksanitizedparameters/global-input-sanitizer' => [
            'target' => \DMK\MkSanitizedParameters\Middleware\GlobalInputSanitizerMiddleware::class,
            'after' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
            'before' => [
                'typo3/cms-backend/locked-backend',
            ],
        ],
    ],
];
