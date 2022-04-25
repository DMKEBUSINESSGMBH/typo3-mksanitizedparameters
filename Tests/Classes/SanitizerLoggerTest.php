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
use DMK\MkSanitizedParameters\Utility\FilterUtility;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SanitizerLoggerTest extends AbstractTestCase
{
    /**
     * @test
     * @group unit
     */
    public function sanitizeInputDoesNotCallLoggerIfLoggerNotEnabledAndValueNotChanged()
    {
        // enable debug mode
        $this->setExtConf(['logMode' => 0]);
        // set common rule (all a string)
        $this->addRules([Rules::COMMON_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS]);

        $logger = $this->prophesize(Logger::class);
        $logger->warning()->shouldNotBeCalled();
        GeneralUtility::addInstance(Logger::class, $logger->reveal());

        $filter = $this->prophesize(FilterUtility::class);
        $filter->isValueChanged('bar', 'bar')->willReturn(false);
        GeneralUtility::addInstance(FilterUtility::class, $filter->reveal());

        $input = Factory::createInput(ArrayInput::class, 'TestInput', ['foo' => 'bar']);
        Factory::getSanitizer()->sanitizeInput($input);

        $this->assertSame(['foo' => 'bar'], $input->getInputArray());
    }

    /**
     * @test
     * @group unit
     */
    public function sanitizeInputDoesNotCallLoggerIfLoggingNotEnabledAndValueChanged()
    {
        // enable debug mode
        $this->setExtConf(['logMode' => 0]);
        // set common rule (all a string)
        $this->addRules([Rules::COMMON_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS]);

        $logger = $this->prophesize(Logger::class);
        $logger->warning()->shouldNotBeCalled();
        GeneralUtility::addInstance(Logger::class, $logger->reveal());

        $filter = $this->prophesize(FilterUtility::class);
        $filter->isValueChanged('bar', 'bar')->willReturn(true);
        GeneralUtility::addInstance(FilterUtility::class, $filter->reveal());

        $input = Factory::createInput(ArrayInput::class, 'TestInput', ['foo' => 'bar']);
        Factory::getSanitizer()->sanitizeInput($input);

        $this->assertSame(['foo' => 'bar'], $input->getInputArray());
    }

    /**
     * @test
     * @group unit
     */
    public function sanitizeInputDoesNotCallLoggerIfLoggingEnabledAndValueNotChanged()
    {
        // enable debug mode
        $this->setExtConf(['logMode' => 1]);
        // set common rule (all a string)
        $this->addRules([Rules::COMMON_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS]);

        $logger = $this->prophesize(Logger::class);
        $logger->warning()->shouldNotBeCalled();
        GeneralUtility::addInstance(Logger::class, $logger->reveal());

        $filter = $this->prophesize(FilterUtility::class);
        $filter->isValueChanged('bar', 'bar')->willReturn(false);
        GeneralUtility::addInstance(FilterUtility::class, $filter->reveal());

        $input = Factory::createInput(ArrayInput::class, 'TestInput', ['foo' => 'bar']);
        Factory::getSanitizer()->sanitizeInput($input);

        $this->assertSame(['foo' => 'bar'], $input->getInputArray());
    }

    /**
     * @test
     * @group unit
     */
    public function sanitizeInputCallsLoggerCorrectIfLoggingEnabledAndValueChanged()
    {
        // enable debug mode
        $this->setExtConf(['logMode' => 1]);
        // set common rule (all a string)
        $this->addRules([Rules::COMMON_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS]);

        $logger = $this->prophesize(Logger::class);
        $logger->warning(
            Sanitizer::MESSAGE_VALUE_HAS_CHANGED,
            [
                'Parameter Name:' => 'foo',
                'initialer Wert:' => 'bar',
                'Wert nach Bereinigung:' => 'bar',
                'komplettes Parameter Array' => ['foo' => 'bar'],
            ]
        );
        GeneralUtility::addInstance(Logger::class, $logger->reveal());

        $filter = $this->prophesize(FilterUtility::class);
        $filter->isValueChanged('bar', 'bar')->willReturn(true);
        GeneralUtility::addInstance(FilterUtility::class, $filter->reveal());

        $input = Factory::createInput(ArrayInput::class, 'TestInput', ['foo' => 'bar']);
        Factory::getSanitizer()->sanitizeInput($input);

        $this->assertSame(['foo' => 'bar'], $input->getInputArray());
    }
}
