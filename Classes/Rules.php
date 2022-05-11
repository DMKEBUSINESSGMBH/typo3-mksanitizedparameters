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

namespace DMK\MkSanitizedParameters;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class to register and retrieve rules for
 * the parameters in the system.
 *
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class Rules
{
    /**
     * @var string
     */
    public const COMMON_RULES_KEY = '__common';

    /**
     * @var string
     */
    public const DEFAULT_RULES_KEY = '__default';

    /**
     * @var bool
     */
    protected static $defaultsLoaded = false;

    /**
     * @var array<string, mixed>
     */
    protected static $rulesForFrontend = [];

    /**
     * @var array<string, mixed>
     */
    protected static $rulesForBackend = [];

    /**
     * the default rules for common TYPO3 request parameters.
     * add your own parameter rules in localconf.php similar to the
     * rules below or overwrite them. examples for the rules possibilities
     * can be found in documentation or Sanitizer class.
     */
    public static function loadDefaults(): void
    {
        if (self::$defaultsLoaded) {
            return;
        }
        self::$defaultsLoaded = true;

        require_once ExtensionManagementUtility::extPath(
            'mksanitizedparameters',
            'Configuration/SanitizeRules/Default.php'
        );

        if (ExtensionManagementUtility::isLoaded('caretaker_instance')) {
            require_once ExtensionManagementUtility::extPath(
                'mksanitizedparameters',
                'Configuration/SanitizeRules/CaretakerInstance.php'
            );
        }

        if (ExtensionManagementUtility::isLoaded('mksearch')) {
            require_once ExtensionManagementUtility::extPath(
                'mksanitizedparameters',
                'Configuration/SanitizeRules/Mksearch.php'
            );
        }

        if (ExtensionManagementUtility::isLoaded('fluid_recommendation')) {
            require_once ExtensionManagementUtility::extPath(
                'mksanitizedparameters',
                'Configuration/SanitizeRules/FluidRecommendation.php'
            );
        }

        if (ExtensionManagementUtility::isLoaded('powermail')) {
            require_once ExtensionManagementUtility::extPath(
                'mksanitizedparameters',
                'Configuration/SanitizeRules/Powermail.php'
            );
        }

        if (ExtensionManagementUtility::isLoaded('form')) {
            require_once ExtensionManagementUtility::extPath(
                'mksanitizedparameters',
                'Configuration/SanitizeRules/Form.php'
            );
        }
    }

    /**
     * look into the doc of
     * tx_mksanitizedparameters::sanitizeArrayByRules()
     * to see how the rules must be passed.
     *
     * @param array<string, mixed> $rules
     */
    public static function addRulesForFrontend(
        array $rules
    ): void {
        ArrayUtility::mergeRecursiveWithOverrule(self::$rulesForFrontend, $rules);
    }

    /**
     * look into the doc of
     * tx_mksanitizedparameters::sanitizeArrayByRules()
     * to see how the rules must be passed.
     *
     * @param array<string, mixed> $rules
     */
    public static function addRulesForBackend(
        array $rules
    ): void {
        ArrayUtility::mergeRecursiveWithOverrule(self::$rulesForBackend, $rules);
    }

    /**
     * the default environment is Frontend.
     *
     * @return array<string, mixed>
     */
    public static function getRulesForCurrentEnvironment(): array
    {
        if (!(($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface)
            || ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()
        ) {
            $rules = self::getRulesForFrontend();
        } else {
            $rules = self::getRulesForBackend();
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getRulesForFrontend()
    {
        return self::$rulesForFrontend;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getRulesForBackend()
    {
        return self::$rulesForBackend;
    }
}
