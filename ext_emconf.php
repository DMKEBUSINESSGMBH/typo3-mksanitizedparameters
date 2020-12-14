<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "mksanitizedparameters".
 *
 * Auto generated 17-09-2014 18:11
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/
$EM_CONF['mksanitizedparameters'] = [
    'title' => 'MK Sanitized Parameters',
    'description' => 'Sanitize $_REQUEST, $_POST and $_GET before the processing of TYPO3 in backend or frontend starts. Take a look into the documentation how to add your own rules or see which one exist.',
    'category' => 'misc',
    'author' => 'Hannes Bochmann',
    'author_email' => 'dev@dmk-ebusiness.de',
    'author_company' => 'DMK E-BUSINESS GmbH',
    'shy' => '',
    'dependencies' => '',
    'version' => '9.5.1',
    'conflicts' => '',
    'priority' => 'top',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'suggests' => [],
    'autoload' => [
        'classmap' => [
            'Classes/',
            'sanitizer',
            'hooks',
            'class.tx_mksanitizedparameters.php',
            'class.tx_mksanitizedparameters_Rules.php',
            'class.tx_mksanitizedparameters_StealthMode.php',
        ],
    ],
];
