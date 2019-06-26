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
 * @author Hannes Bochmann <dev@dmk-ebusiness.de>
 */
class tx_mksanitizedparameters_sanitizer_Alnum_testcase extends tx_rnbase_tests_BaseTestCase
{
    /**
     * @group unit
     */
    public function testSanitizeValueRemovesNonLettersAndNonDigits()
    {
        $testString = 'abc123#! def';

        $this->assertEquals(
            'abc123def',
            tx_mksanitizedparameters_sanitizer_Alnum::sanitizeValue($testString),
            'String was not sanitized correct.'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeValueAllowingWhitespacesRemovesNonLettersAndNonDigits()
    {
        $testString = 'abc123#! def';

        $this->assertEquals(
            'abc123 def',
            tx_mksanitizedparameters_sanitizer_Alnum::sanitizeValueAllowingWhitespaces($testString),
            'String was not sanitized correct.'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeValueWithUmlauts()
    {
        $testString = 'äbc';

        $this->assertEquals(
            'äbc',
            tx_mksanitizedparameters_sanitizer_Alnum::sanitizeValue($testString),
            'String was not sanitized correct.'
        );
    }
}
