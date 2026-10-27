<?php

declare(strict_types=1);

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mksanitizedparameters" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use Rector\Config\RectorConfig;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;
use Ssch\TYPO3Rector\CodeQuality\General\ConvertImplicitVariablesToExplicitGlobalsRector;
use Ssch\TYPO3Rector\CodeQuality\General\ExtEmConfRector;
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;

return RectorConfig::configure()
    ->withPaths([__DIR__])
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withSets([
        LevelSetList::UP_TO_PHP_85,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        Typo3LevelSetList::UP_TO_TYPO3_14,
    ])
//    ->withPreparedSets(strictBooleans: true)
    ->withRules([
        ConvertImplicitVariablesToExplicitGlobalsRector::class,
    ])
    ->withConfiguredRule(
        ExtEmConfRector::class,
        [
            ExtEmConfRector::PHP_VERSION_CONSTRAINT => '8.2.0-8.5.99',
            ExtEmConfRector::TYPO3_VERSION_CONSTRAINT => '13.4.0-14.3.99',
            ExtEmConfRector::ADDITIONAL_VALUES_TO_BE_REMOVED => [],
        ]
    )
    ->withPHPStanConfigs([
        Typo3Option::PHPSTAN_FOR_RECTOR_PATH,
        __DIR__.'/phpstan.neon',
    ])
    ->withSkip([
        // no namespace imports for these files:
        NameImportingPostRector::class => [
            'ext_localconf.php',
            'ext_tables.php',
            __DIR__.'/Configuration/*.php',
            __DIR__.'/Configuration/**/*.php',
        ],
    ]);
