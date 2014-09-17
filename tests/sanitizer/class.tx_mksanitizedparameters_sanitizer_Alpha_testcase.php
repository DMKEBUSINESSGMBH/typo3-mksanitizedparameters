<?php
/**
 *
 *  Copyright notice
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
 * include required classes
 */
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_mksanitizedparameters_sanitizer_Alpha');
	
/**
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <dev@dmk-ebusiness.de>
 */
class tx_mksanitizedparameters_sanitizer_Alpha_testcase extends tx_phpunit_testcase {
	
	/**
	 * @group unit
	 */
	public function testSanitizeValueRemovesNonLetters(){
		$testString = 'abc123#! def';
		
		$this->assertEquals(
			'abcdef', 
			tx_mksanitizedparameters_sanitizer_Alpha::sanitizeValue($testString),
			'String was not sanitized correct.'
		);
	}
	
	/**
	 * @group unit
	 */
	public function testSanitizeValueAllowingWhitespacesRemovesNonLetters(){
		$testString = 'abc123#! def';
		
		$this->assertEquals(
			'abc def', 
			tx_mksanitizedparameters_sanitizer_Alpha::sanitizeValueAllowingWhitespaces($testString),
			'String was not sanitized correct.'
		);
	}
	
	/**
	 * @group unit
	 */
	public function testSanitizeValueWithUmlauts(){
		$testString = 'äbc';
		
		$this->assertEquals(
			'äbc', 
			tx_mksanitizedparameters_sanitizer_Alpha::sanitizeValue($testString),
			'String was not sanitized correct.'
		);
	}
}