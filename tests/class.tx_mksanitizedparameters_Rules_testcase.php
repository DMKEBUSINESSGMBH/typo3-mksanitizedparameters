<?php
/**
 *  Copyright notice.
 *
 *  (c) 2012 DMK E-Business GmbH <dev@dmk-ebusiness.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * we need a possibility to clear the rules for the tests.
 */
class tx_mksanitizedparameters_tests_Rules extends tx_mksanitizedparameters_Rules
{
    public static function clearRules()
    {
        self::$rulesForBackend = array();
        self::$rulesForFrontend = array();
    }
}

/**
 * @author Hannes Bochmann <dev@dmk-ebusiness.de>
 */
class tx_mksanitizedparameters_Rules_testcase extends tx_rnbase_tests_BaseTestCase
{
    /**
     * (non-PHPdoc).
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        tx_mksanitizedparameters_tests_Rules::clearRules();
    }

    /**
     * (non-PHPdoc).
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        tx_mksanitizedparameters_tests_Rules::clearRules();
    }

    /**
     * @group unit
     */
    public function testAddingRulesInFrontend()
    {
        $rulesForFrontend = array(
            'myParameterRule' => FILTER_SANITIZE_STRING,
        );
        tx_mksanitizedparameters_tests_Rules::addRulesForFrontend(
            $rulesForFrontend
        );

        $addedParameterRules =
            tx_mksanitizedparameters_tests_Rules::getRulesForFrontend();

        $this->assertSame(
            $rulesForFrontend,
            $addedParameterRules,
            'The rules were not added!'
        );

        $this->assertEmpty(
            tx_mksanitizedparameters_tests_Rules::getRulesForBackend(),
            'parameter rules were added for backend'
        );
    }

    /**
     * @group unit
     */
    public function testAddingRulesInBackend()
    {
        $rulesForBackend = array(
            'myParameterRule' => FILTER_SANITIZE_STRING,
        );
        tx_mksanitizedparameters_tests_Rules::addRulesForBackend(
            $rulesForBackend
        );

        $addedParameterRules =
            tx_mksanitizedparameters_tests_Rules::getRulesForBackend();

        $this->assertSame(
            $rulesForBackend,
            $addedParameterRules,
            'The rules were not added!'
        );

        $this->assertEmpty(
            tx_mksanitizedparameters_tests_Rules::getRulesForFrontend(),
            'parameter rules were added for frontend'
        );
    }

    /**
     * @group unit
     */
    public function testGetRulesForCurrentEnvironment()
    {
        $rulesForBackend = array(
            'myParameterRule' => FILTER_SANITIZE_STRING,
        );
        tx_mksanitizedparameters_tests_Rules::addRulesForBackend(
            $rulesForBackend
        );

        $addedParameterRules =
            tx_mksanitizedparameters_tests_Rules::getRulesForCurrentEnvironment();

        $this->assertSame(
            $rulesForBackend,
            $addedParameterRules,
            'The rules were not added!'
        );

        $this->assertEmpty(
            tx_mksanitizedparameters_tests_Rules::getRulesForFrontend(),
            'parameter rules were added for frontend'
        );
    }

    /**
     * @group unit
     */
    public function testAddingRulesWithSeveralSubsequentCalls()
    {
        $rulesForFrontend = array(
            'myParameterRule' => FILTER_SANITIZE_STRING,
        );
        tx_mksanitizedparameters_tests_Rules::addRulesForFrontend(
            $rulesForFrontend
        );

        $otherRulesForFrontend = array(
            'myOtherParameterRule' => FILTER_SANITIZE_STRING,
        );
        tx_mksanitizedparameters_tests_Rules::addRulesForFrontend(
            $otherRulesForFrontend
        );

        $addedParameterRules =
            tx_mksanitizedparameters_tests_Rules::getRulesForFrontend();

        $this->assertSame(
            array_merge($rulesForFrontend, $otherRulesForFrontend),
            $addedParameterRules,
            'The rules were not added correct!'
        );
    }

    /**
     * @group unit
     */
    public function testAddingRulesWithSeveralSubsequentCallsOverwrittingCorrect()
    {
        $rulesForFrontend = array(
            'myParameterRule' => FILTER_SANITIZE_STRING,
        );
        tx_mksanitizedparameters_tests_Rules::addRulesForFrontend(
            $rulesForFrontend
        );

        $overwriteRulesForFrontend = array(
            'myParameterRule' => FILTER_SANITIZE_EMAIL,
        );
        tx_mksanitizedparameters_tests_Rules::addRulesForFrontend(
            $overwriteRulesForFrontend
        );

        $addedParameterRules =
            tx_mksanitizedparameters_tests_Rules::getRulesForFrontend();

        $this->assertSame(
            $overwriteRulesForFrontend,
            $addedParameterRules,
            'The first rules were not overwritten!'
        );
    }

    /**
     * @group unit
     */
    public function testCommonRulesAreMergedCorrectWhenAdded()
    {
        $rulesForFrontend = array(
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
                'firstCommon' => FILTER_SANITIZE_STRING,
                'secondCommon' => FILTER_SANITIZE_ENCODED,
            ),
        );
        tx_mksanitizedparameters_tests_Rules::addRulesForFrontend(
            $rulesForFrontend
        );

        $overwriteRulesForFrontend = array(
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
                'firstCommon' => FILTER_SANITIZE_NUMBER_INT,
                'thirdCommon' => FILTER_SANITIZE_ENCODED,
            ),
        );
        tx_mksanitizedparameters_tests_Rules::addRulesForFrontend(
            $overwriteRulesForFrontend
        );

        $addedParameterRules =
            tx_mksanitizedparameters_tests_Rules::getRulesForFrontend();

        $expectedRules = array(
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
                'firstCommon' => FILTER_SANITIZE_NUMBER_INT,
                'secondCommon' => FILTER_SANITIZE_ENCODED,
                'thirdCommon' => FILTER_SANITIZE_ENCODED,
            ),
        );
        $this->assertSame(
            $expectedRules,
            $addedParameterRules,
            'The common rules were not merged correct!'
        );
    }
}
