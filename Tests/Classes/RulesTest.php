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

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class RulesTest extends AbstractTestCase
{
    /**
     * @test
     * @group unit
     */
    public function addingRulesInFrontend()
    {
        $rules = ['myParameterRule' => FILTER_SANITIZE_FULL_SPECIAL_CHARS];
        Rules::addRulesForFrontend($rules);
        $this->assertSame($rules, Rules::getRulesForFrontend());
        $this->assertEmpty(Rules::getRulesForBackend());
    }

    /**
     * @test
     * @group unit
     */
    public function addingRulesInBackend()
    {
        $rules = ['myParameterRule' => FILTER_SANITIZE_FULL_SPECIAL_CHARS];
        Rules::addRulesForBackend($rules);
        $this->assertSame($rules, Rules::getRulesForBackend());
        $this->assertEmpty(Rules::getRulesForFrontend());
    }

    /**
     * @test
     * @group unit
     */
    public function getRulesForCurrentEnvironment()
    {
        $rules = ['myParameterRule' => FILTER_SANITIZE_FULL_SPECIAL_CHARS];
        $this->addRules($rules);
        $this->assertSame(
            $rules,
            Rules::getRulesForCurrentEnvironment()
        );
    }

    /**
     * @test
     * @group unit
     */
    public function addRulesWithSeveralSubsequentCalls()
    {
        $rulesSet1 = ['myParameterRule' => FILTER_SANITIZE_FULL_SPECIAL_CHARS];
        Rules::addRulesForFrontend($rulesSet1);
        $rulesSet2 = ['myOtherParameterRule' => FILTER_SANITIZE_FULL_SPECIAL_CHARS];
        Rules::addRulesForFrontend($rulesSet2);
        $this->assertSame(array_merge($rulesSet1, $rulesSet2), Rules::getRulesForFrontend());
    }

    /**
     * @test
     * @group unit
     */
    public function addRulesWithSeveralSubsequentCallsOverwrittingCorrect()
    {
        $rules = ['myParameterRule' => FILTER_SANITIZE_FULL_SPECIAL_CHARS];
        Rules::addRulesForFrontend($rules);
        $rulesOverridden = ['myParameterRule' => FILTER_SANITIZE_EMAIL];
        Rules::addRulesForFrontend($rulesOverridden);
        $this->assertSame($rulesOverridden, Rules::getRulesForFrontend());
    }

    /**
     * @test
     * @group unit
     */
    public function commonRulesAreMergedCorrectWhenAdded()
    {
        Rules::addRulesForFrontend(
            [
                Rules::COMMON_RULES_KEY => [
                    'firstCommon' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                    'secondCommon' => FILTER_SANITIZE_ENCODED,
                ],
            ]
        );
        Rules::addRulesForFrontend(
            [
                Rules::COMMON_RULES_KEY => [
                    'firstCommon' => FILTER_SANITIZE_NUMBER_INT,
                    'thirdCommon' => FILTER_SANITIZE_ENCODED,
                ],
            ]
        );
        $this->assertSame(
            [
                Rules::COMMON_RULES_KEY => [
                    'firstCommon' => FILTER_SANITIZE_NUMBER_INT,
                    'secondCommon' => FILTER_SANITIZE_ENCODED,
                    'thirdCommon' => FILTER_SANITIZE_ENCODED,
                ],
            ],
            Rules::getRulesForFrontend()
        );
    }
}
