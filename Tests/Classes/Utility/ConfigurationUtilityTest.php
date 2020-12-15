<?php

declare(strict_types=1);

namespace DMK\MkSanitizedParameters\Utility;

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

use DMK\MkSanitizedParameters\AbstractTestCase;
use DMK\MkSanitizedParameters\Factory;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ConfigurationUtilityTest extends AbstractTestCase
{
    /**
     * @test
     * @group unit
     */
    public function isDebugModeFalse()
    {
        $this->setExtConf(['debugMode' => 0]);
        $this->assertFalse(Factory::getConfiguration()->isDebugMode());
    }

    /**
     * @test
     * @group unit
     */
    public function isDebugModeTrue()
    {
        $this->setExtConf(['debugMode' => 1]);
        $this->assertTrue(Factory::getConfiguration()->isDebugMode());
    }

    /**
     * @test
     * @group unit
     */
    public function isLogModeFalse()
    {
        $this->setExtConf(['logMode' => 0]);
        $this->assertFalse(Factory::getConfiguration()->isLogMode());
    }

    /**
     * @test
     * @group unit
     */
    public function isLogModeModeTrue()
    {
        $this->setExtConf(['logMode' => 1]);
        $this->assertTrue(Factory::getConfiguration()->isLogMode());
    }

    /**
     * @test
     * @group unit
     */
    public function isStealthModeFalse()
    {
        $this->setExtConf(['stealthMode' => 0]);
        $this->assertFalse(Factory::getConfiguration()->isStealthMode());
    }

    /**
     * @test
     * @group unit
     */
    public function isStealthModeTrue()
    {
        $this->setExtConf(['stealthMode' => 1]);
        $this->assertTrue(Factory::getConfiguration()->isStealthMode());
    }

    /**
     * @test
     * @group unit
     */
    public function isStealthModeFive()
    {
        $this->setExtConf(['stealthModeStoragePid' => 5]);
        $this->assertSame(5, Factory::getConfiguration()->getStealthModeStoragePid());
    }

    /**
     * @test
     * @group unit
     */
    public function isStealthModeSeven()
    {
        $this->setExtConf(['stealthModeStoragePid' => 7]);
        $this->assertSame(7, Factory::getConfiguration()->getStealthModeStoragePid());
    }
}
