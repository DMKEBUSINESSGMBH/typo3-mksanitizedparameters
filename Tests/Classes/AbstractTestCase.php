<?php

declare(strict_types=1);

namespace DMK\MkSanitizedParameters;

/***************************************************************
 * Copyright notice
 *
 * (c) 2020 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use DMK\MkSanitizedParameters\Utility\DebugUtility;
use ReflectionClass;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array<string, string|array>
     */
    protected $backup = [];

    protected function setUp()
    {
        $this->backup = [
            '_SERVER' => $_SERVER,
            'TYPO3_CONF_VARS' => $GLOBALS['TYPO3_CONF_VARS'],
        ];
        $_SERVER['REMOTE_ADDR'] = 'testip';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'] = 'testip';

        $this->setExtConf(
            [
                'debugMode' => 0,
                'logMode' => 0,
            ]
        );
    }

    protected function tearDown()
    {
        $_SERVER = $this->backup['_SERVER'];
        $GLOBALS['TYPO3_CONF_VARS'] = $this->backup['TYPO3_CONF_VARS'];

        $this->resetRules();
        $this->resetDebugger();
    }

    /**
     * Sets extension configuration to configuration utility.
     *
     * @param array $extConf
     */
    protected static function setExtConf(array $extConf)
    {
        $config = \DMK\MkSanitizedParameters\Factory::getConfiguration();
        // force ext conf creation
        $config->isStealthMode();
        // now override the extconf array property
        $reflector = new ReflectionClass(get_class($config));
        $property = $reflector->getProperty('extensionConfiguration');
        $property->setAccessible(true);
        $property->setValue(
            $config,
            array_merge(
                $property->getValue($config),
                $extConf
            )
        );
    }

    /**
     * Remove all rules from rules config class.
     */
    protected static function resetRules()
    {
        $rulesReflection = new ReflectionClass(Rules::class);

        $rulesForFrontend = $rulesReflection->getProperty('rulesForFrontend');
        $rulesForFrontend->setAccessible(true);
        $rulesForFrontend->setValue(null, []);

        $rulesForBackend = $rulesReflection->getProperty('rulesForBackend');
        $rulesForBackend->setAccessible(true);
        $rulesForBackend->setValue(null, []);
    }

    /**
     * Remove all rules from rules config class.
     */
    protected static function resetDebugger()
    {
        // first remove all debugs
        $debuggerReflection = new ReflectionClass(DebugUtility::class);
        $debugStackReflection = $debuggerReflection->getProperty('debugStack');
        $debugStackReflection->setAccessible(true);
        $debugStackReflection->setValue(Factory::getDebugger(), []);

        // now destroy instance
        GeneralUtility::removeSingletonInstance(
            DebugUtility::class,
            Factory::getDebugger()
        );
    }

    /**
     * @param array<string, int|string|array> $rules
     */
    protected static function addRules(array $rules)
    {
        switch (TYPO3_MODE) {
            case 'FE':
            default:
                Rules::addRulesForFrontend($rules);
                break;
            case 'BE':
                Rules::addRulesForBackend($rules);
                break;
        }
    }
}