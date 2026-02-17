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

use DMK\MkSanitizedParameters\Input\ArrayInput;
use DMK\MkSanitizedParameters\Utility\DebugUtility;
use DMK\MkSanitizedParameters\Utility\FilterUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SanitizerDebuggerTest extends AbstractTestCase
{
    public function testSanitizeInputDoesNotCallDebuggerIfDebuggingNotEnabledAndValueNotChanged(): void
    {
        // enable debug mode
        $this->setExtConf(['debugMode' => 0]);
        // set common rule (all a string)
        $this->addRules([Rules::COMMON_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS]);

        $debugger = $this->prophesize(DebugUtility::class);
        $debugger->debug()->shouldNotBeCalled();
        GeneralUtility::setSingletonInstance(DebugUtility::class, $debugger->reveal());

        $filter = $this->prophesize(FilterUtility::class);
        $filter->isValueChanged('bar', 'bar')->willReturn(false);
        GeneralUtility::addInstance(FilterUtility::class, $filter->reveal());

        $input = Factory::createInput(ArrayInput::class, 'TestInput', ['foo' => 'bar']);
        Factory::getSanitizer()->sanitizeInput($input);

        $this->assertSame(['foo' => 'bar'], $input->getInputArray());
    }

    public function testSanitizeInputDoesNotCallDebuggerIfDebuggingNotEnabledAndValueChanged(): void
    {
        // enable debug mode
        $this->setExtConf(['debugMode' => 0]);
        // set common rule (all a string)
        $this->addRules([Rules::COMMON_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS]);

        $debugger = $this->prophesize(DebugUtility::class);
        $debugger->debug()->shouldNotBeCalled();
        GeneralUtility::setSingletonInstance(DebugUtility::class, $debugger->reveal());

        $filter = $this->prophesize(FilterUtility::class);
        $filter->isValueChanged('bar', 'bar')->willReturn(true);
        GeneralUtility::addInstance(FilterUtility::class, $filter->reveal());

        $input = Factory::createInput(ArrayInput::class, 'TestInput', ['foo' => 'bar']);
        Factory::getSanitizer()->sanitizeInput($input);

        $this->assertSame(['foo' => 'bar'], $input->getInputArray());
    }

    public function testSanitizeInputDoesNotCallDebuggerIfDebuggingEnabledAndValueNotChanged(): void
    {
        // enable debug mode
        $this->setExtConf(['debugMode' => 1]);
        // set common rule (all a string)
        $this->addRules([Rules::COMMON_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS]);

        $debugger = $this->prophesize(DebugUtility::class);
        $debugger->debug()->shouldNotBeCalled();
        GeneralUtility::setSingletonInstance(DebugUtility::class, $debugger->reveal());

        $filter = $this->prophesize(FilterUtility::class);
        $filter->isValueChanged('bar', 'bar')->willReturn(false);
        GeneralUtility::addInstance(FilterUtility::class, $filter->reveal());

        $input = Factory::createInput(ArrayInput::class, 'TestInput', ['foo' => 'bar']);
        Factory::getSanitizer()->sanitizeInput($input);

        $this->assertSame(['foo' => 'bar'], $input->getInputArray());
    }

    public function testSanitizeInputCallsDebuggerCorrectIfDebuggingEnabledAndValueChanged(): void
    {
        // enable debug mode
        $this->setExtConf(['debugMode' => 1]);
        // set common rule (all a string)
        $this->addRules([Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS]);

        $debugger = $this->prophesize(DebugUtility::class);
        $debugger->debug(
            [
                'parameter name' => 'foo',
                'initial value' => '"bar',
                'sanitized value' => '&quot;bar',
                'complete parameter array' => ['foo' => '&quot;bar'],
            ]
        )->shouldBeCalled();
        GeneralUtility::setSingletonInstance(DebugUtility::class, $debugger->reveal());

        $input = Factory::createInput(ArrayInput::class, 'TestInput', ['foo' => '"bar']);
        Factory::getSanitizer()->sanitizeInput($input);

        $this->assertSame(['foo' => '&quot;bar'], $input->getInputArray());
    }
}
