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

namespace DMK\MkSanitizedParameters\Utility;

use DMK\MkSanitizedParameters\AbstractTestCase;
use DMK\MkSanitizedParameters\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ConfigurationUtilityTest extends AbstractTestCase
{
    public function testIsDebugModeFalse(): void
    {
        $this->setExtConf(['debugMode' => 0]);
        $this->assertFalse(Factory::getConfiguration()->isDebugMode());
    }

    public function testIsDebugModeTrue(): void
    {
        $this->setExtConf(['debugMode' => 1]);
        $this->assertTrue(Factory::getConfiguration()->isDebugMode());
    }

    public function testIsLogModeFalse(): void
    {
        $this->setExtConf(['logMode' => 0]);
        $this->assertFalse(Factory::getConfiguration()->isLogMode());
    }

    public function testIsLogModeModeTrue(): void
    {
        $this->setExtConf(['logMode' => 1]);
        $this->assertTrue(Factory::getConfiguration()->isLogMode());
    }

    public function testIsStealthModeFalse(): void
    {
        $this->setExtConf(['stealthMode' => 0]);
        $this->assertFalse(Factory::getConfiguration()->isStealthMode());
    }

    public function testIsStealthModeTrue(): void
    {
        $this->setExtConf(['stealthMode' => 1]);
        $this->assertTrue(Factory::getConfiguration()->isStealthMode());
    }

    public function testIsStealthModeFive(): void
    {
        $this->setExtConf(['stealthModeStoragePid' => 5]);
        $this->assertSame(5, Factory::getConfiguration()->getStealthModeStoragePid());
    }

    public function testIsStealthModeSeven(): void
    {
        $this->setExtConf(['stealthModeStoragePid' => 7]);
        $this->assertSame(7, Factory::getConfiguration()->getStealthModeStoragePid());
    }

    #[DataProvider('getExtensionConfigurationLoadsCorrectData')]
    public function testGetExtensionConfigurationLoadsCorrect(array $configuration, bool $isDebugMode, bool $isLogMode, bool $isStealthMode, int $stealthModeStoragePid): void
    {
        $config = Factory::getConfiguration();
        // now override the extconf array property
        $configReflection = new \ReflectionObject($config);
        $extensionConfigurationProperty = $configReflection->getProperty('extensionConfiguration');
        $extensionConfigurationProperty->setValue($config, null);

        // config loading for typo3 9 or later
        $extensionConfiguration = $this->prophesize(ExtensionConfiguration::class);
        $extensionConfiguration
            ->get('mksanitizedparameters', '')
            ->shouldBeCalledOnce()
            ->willReturn($configuration);
        GeneralUtility::addInstance(ExtensionConfiguration::class, $extensionConfiguration->reveal());

        $extensionConfigurationMethod = $configReflection->getMethod('getExtensionConfiguration');
        $this->assertSame(
            'leer',
            $extensionConfigurationMethod->invokeArgs($config, ['gibtEsNicht', 'leer'])
        );

        $this->assertSame($isDebugMode, Factory::getConfiguration()->isDebugMode());
        $this->assertSame($isLogMode, Factory::getConfiguration()->isLogMode());
        $this->assertSame($isStealthMode, Factory::getConfiguration()->isStealthMode());
        $this->assertSame($stealthModeStoragePid, Factory::getConfiguration()->getStealthModeStoragePid());
    }

    /**
     * Testdata for getExtensionConfigurationLoadsCorrect.
     *
     * @return array[]
     */
    public static function getExtensionConfigurationLoadsCorrectData(): array
    {
        return [
            __LINE__.':1,1,0,14' => [
                [
                    'debugMode' => '1',
                    'logMode' => 'true',
                    'stealthMode' => '0',
                    'stealthModeStoragePid' => '14',
                ],
                true,
                true,
                false,
                14,
            ],
            __LINE__.'0,0,1,57' => [
                [
                    'debugMode' => '0',
                    'logMode' => '0',
                    'stealthMode' => 'false',
                    'stealthModeStoragePid' => '57acht',
                ],
                false,
                false,
                true,
                57,
            ],
        ];
    }
}
