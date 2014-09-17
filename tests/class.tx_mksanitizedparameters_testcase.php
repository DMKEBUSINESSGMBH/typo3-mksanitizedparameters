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
tx_rnbase::load('tx_mksanitizedparameters');
tx_rnbase::load('tx_mklib_tests_Util');
tx_rnbase::load('tx_rnbase_util_Logger');
tx_rnbase::load('tx_rnbase_util_Debug');

/**
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <dev@dmk-ebusiness.de>
 */
class tx_mksanitizedparameters_testcase extends tx_phpunit_testcase {

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		tx_mklib_tests_Util::disableDevlog();
		tx_mklib_tests_Util::storeExtConf('mksanitizedparameters');
		tx_mklib_tests_Util::setExtConfVar('debugMode', 0, 'mksanitizedparameters');
		tx_mklib_tests_Util::setExtConfVar('logMode', 0, 'mksanitizedparameters');
	}

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown(){
		tx_mklib_tests_Util::restoreExtConf('mksanitizedparameters');
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesReturnsUntouchedArrayIfRulesEmpty(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' => 'testValue'
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, array()
		);

		$this->assertSame(
			$arrayToSanitize, $sanitizedArray, 'The array was touched!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesReturnsUntouchedArrayWithoutRulesForGivenParameter(){
		$arrayToSanitize = array(
			'parameterNameWithoutRules' => 'testValue'
		);
		$rules = array(
			'unexistentParameter'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertSame(
			$arrayToSanitize, $sanitizedArray, 'The array was touched!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWorksCorrectWithUnconfiguredValuesButDefaultRules(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> '1testValue'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> FILTER_SANITIZE_NUMBER_INT
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array('parameterNameToBeSanitized' 	=> '1'),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWorksCorrectWithFlatArrayAndSingleFilterConfig(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> '1testValue'
		);
		$rules = array(
			'parameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array('parameterNameToBeSanitized' 	=> '1'),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWorksCorrectWithFlatArrayAndFilterConfigAsArray(){
		$arrayToSanitize = array(
			'parameterInRange' 		=> '<span>me&you</span>',
			'parameterOutOfRange' 	=> '<span>me&you</span>'
		);
		$rules = array(
			'parameterInRange'	=> array(
				'filter'    => FILTER_SANITIZE_STRING,
			),
			'parameterOutOfRange'	=> array(
				'filter'    => FILTER_SANITIZE_STRING,
                'flags'   	=> FILTER_FLAG_ENCODE_AMP
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array(
				'parameterInRange' 		=> 'me&you',
				'parameterOutOfRange' 	=> 'me&#38;you',
			),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWorksCorrectWithUnconfiguredValuesAndNoDefaultRules(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 		=> '1testValue',
			'parameterNameNotToBeSanitized' 	=> '1testValue',
		);
		$rules = array(
			'parameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array(
				'parameterNameToBeSanitized' 		=> '1',
				'parameterNameNotToBeSanitized' 	=> '1testValue',
			),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWorksCorrectWithMultiDimensionalArray(){
		$arrayToSanitize = array(
			'firstExtensionQualifier'	=> array(
				'parameterNameToBeSanitized' 			=> '1testValue',
				'parameterNameToBeSanitizedByDefault' 	=> 'libgd<script>'
			),
			'secondExtensionQualifier'	=> array(
				'subArray'	=> array(
					'parameterNameToBeSanitized' => '<span>me&you</span>'
				)
			),
			'parameterNameToBeSanitizedByDefault' 	=> 'libgd<script>'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_ENCODED,
			'firstExtensionQualifier'	=> array(
				'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT
			),
			'secondExtensionQualifier'	=> array(
				'subArray'	=> array(
					'parameterNameToBeSanitized'	=> array(
						'filter'    => FILTER_SANITIZE_STRING,
		                'flags'   	=> FILTER_FLAG_ENCODE_AMP
					)
				)
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$expectedArray = array(
			'firstExtensionQualifier'	=> array(
				'parameterNameToBeSanitized' 			=> 1,
				'parameterNameToBeSanitizedByDefault' 	=> 'libgd%3Cscript%3E'
			),
			'secondExtensionQualifier'	=> array(
				'subArray'	=> array(
					'parameterNameToBeSanitized' => 'me&#38;you'
				)
			),
			'parameterNameToBeSanitizedByDefault' 	=> 'libgd%3Cscript%3E'
		);

		$this->assertEquals(
			$expectedArray,
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWorksCorrectWithMultiDimensionalArrayAndDefaultRulesOnlyForSubArray(){
		$arrayToSanitize = array(
			'firstExtensionQualifier'	=> array(
				'parameterNameToBeSanitizedByDefault' 	=> '1testValue',
			),
			'parameterNameToBeSanitizedByDefault' 		=> 'libgd<script>'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_ENCODED,
			'firstExtensionQualifier'	=> array(
				tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT
			),
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$expectedArray = array(
			'firstExtensionQualifier'	=> array(
				'parameterNameToBeSanitizedByDefault' 	=> 1,
			),
			'parameterNameToBeSanitizedByDefault' 		=> 'libgd%3Cscript%3E'
		);

		$this->assertEquals(
			$expectedArray,
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWorksCorrectWithSeveralConfiguredFiltersAsFilterArray(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=>
			"<span>Is your name O'reilly & are sure about that?</span>",
		);
		$rules = array(
			'parameterNameToBeSanitized'	=> array(
				'filter'    => array(
					FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
				),
                'flags'   	=> FILTER_FLAG_ENCODE_AMP
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array(
				'parameterNameToBeSanitized'	=>
				'Is your name O&#39;reilly &#38; are sure about that?',
			),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWorksCorrectWithSeveralConfiguredFiltersAsList(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=>
			"<span>Is your name O'reilly & are sure about that?</span>",
		);
		$rules = array(
			'parameterNameToBeSanitized'	=> array(
				FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array(
				'parameterNameToBeSanitized'	=>
				'Is your name O&#39;reilly & are sure about that?',
			),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWorksCorrectWithCustomFilter(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=>
			"abc123",
		);
		$rules = array(
			'parameterNameToBeSanitized'	=> array(
				'filter'    => FILTER_CALLBACK,
               	'options' 	=> array(
               		'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
				)
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array(
				'parameterNameToBeSanitized'	=>
				'abc',
			),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWithTypicalCaretakerRequest(){
		$arrayToSanitize = array (
		  'st' => '1350376881:2f69b091adea4517b68d0d62ac1d2a792f2535462ae5',
		  'd' => '{\\"encrypted\\":\\"Fgi8\\\\/oEXD0CnEQTcGtmfA1Phu7RBD+gZwY2ymllDJ9umVv4USQ\\\\/dGroux\\\\/OrrERyCo7t8DiRoaDW58P6UetoOgu9JLtol320HQVWzVPc\\\\/NObedgTZEo\\\\/He9NjKTpMLq32R77WC5WMblPim4l2Yuq2To+llrhYTgbbXWkgKi9PgQ=:9yxcT\\\\/dymal1z1d1zDVazjnkwSvLovTN5Sj1toYjsley3V0R9u62c+0mK2nf1ogNL3v08Tp7tIuxXU9qeJZNGt95zoGI\\\\/Ntnl1\\\\/OdJiPvHcdt0SIlXk3CAXOvxO2N8gEr3CncX+22RWt6AqtLKOLqHrMerXwShz5AUBfy8SlPOILOIhMvTRr8PeIyxvon7wOBUxXwj21dVL3bYMirCjUpLyvQ68wdTOhmZvEPOq5psxrpIfkGEIgeOpsXTql1ePkRXC6\\\\/5nD\\\\/nAmuTqw\\\\/fRBflgQW8SgWjLwctfjD1vH9meiO6wibuDEKc3iKp2vvFXWEHw\\\\/5CPAia1PPZpmRDrqNpsEI6A00W3WJhpqwXFlR06g1pUyPPt8MrIbr7Ioq+l+PzRpQCbeT0is6F87b2CRCj8jmEJ9F80LCr62n1hiKymwzx2u4+vYaRFt7zaU2pagaFDj3kkIhLNwdQmMsnK\\\\/U9Z8z53D7YiWhbr04rgE2EP8JCN022WPY2hfejXKKSi3RE0TTvo8ixD\\\\/Jh0Lbu3rK0NwY\\\\/6Tnn4dLDzLe0KXvbeMM3+JoNm6AwnL\\\\/zGkZ7IEb8Gol+H7MeEHt1DLmLXmWRuhOA+Co3k\\\\/hw4qMOEQRg+PLNy0AlsWouDCGhnoteWXZPnA4MRx6kDB+v+NhBk2KHx3UeXXP2vXPNhmXzD8iQ8PybmYN45WDmsftK+cHl1OAAC+wO2Z2XCnVTs36c525d6FuX0SlOKEZ\\\\/A6kY7ERTnJ2I4g1lYHAM\\\\/XB19lRfuYGJpJ3mowGL1T6POFqwf6M8Dw8V6LQ9X2QCOQUrO9woq2Jz4LWRxGX0a6mZbvrtpm07tDQeX34lYAEhlL\\\\/nnoqqbI5\\\\/BB03gz4bknhw2hG5K0XoQErEwXEAUugViW0RKKL5O11iRQBXGToZogkecXj1PfCJhLI1UZyEQRmpOP6+19r8sQ+JrJf2GDRHAzqWU2VUHdlNlmd4u22FPnOAGSs2gMDNqBpxs1IA==\\"}',
		  's' => 'acFNX7gVY8wm0mLQbxRaVr8nrnRbPywGb3tjkspp0HC77io/T94qEQbPHMePi2xpNtfpJut9bt2USUBfkCXmc/wKk1Unk7WX7XMoohSuI1BahtNV4DRfGKKpUJV6s+5cD7IET7IFjVLm/wmxs+hl/1Ve1MIjZe2L3VCs4VqmBsg=',
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_URL),
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			$arrayToSanitize,
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesDoesNotCallLoggerIfLoggingNotEnabledAndValueNotChanged(){
		$arrayToSanitize = array (
		  'parameter' => 'test'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_STRING),
		);

		$mksanitizedparameters = $this->getMockClass('tx_mksanitizedparameters', array('getLogger'));

		$mksanitizedparameters::staticExpects($this->never())
			->method('getLogger');

		$mksanitizedparameters::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesDoesNotCallLoggerIfLoggingNotEnabledAndValueChanged(){
		$arrayToSanitize = array (
		  'parameter' => '"test"'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_STRING),
		);

		$mksanitizedparameters = $this->getMockClass(
			'tx_mksanitizedparameters', array('getDebugMode', 'getLogger')
		);

		$mksanitizedparameters::staticExpects($this->any())
			->method('getDebugMode')
			->will($this->returnValue(false));

		$mksanitizedparameters::staticExpects($this->never())
			->method('getLogger');

		$mksanitizedparameters::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesDoesNotCallLoggerIfLoggingEnabledButValueNotChanged(){
		tx_mklib_tests_Util::setExtConfVar('logMode', 1, 'mksanitizedparameters');

		$arrayToSanitize = array (
		  'parameter' => 'test'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_STRING),
		);

		$mksanitizedparameters = $this->getMockClass(
			'tx_mksanitizedparameters', array('getDebugMode', 'getLogger')
		);

		$mksanitizedparameters::staticExpects($this->any())
			->method('getDebugMode')
			->will($this->returnValue(false));

		$mksanitizedparameters::staticExpects($this->never())
			->method('getLogger');

		$mksanitizedparameters::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesDoesNotConsiderValueAsChangedIfHasWhitespaceAtBeginningOrEnd(){
		tx_mklib_tests_Util::setExtConfVar('logMode', 1, 'mksanitizedparameters');

		$arrayToSanitize = array (
			'parameter' => ' test '
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_STRING),
		);

		$mksanitizedparameters = $this->getMockClass(
			'tx_mksanitizedparameters', array('getDebugMode', 'getLogger')
		);

		$mksanitizedparameters::staticExpects($this->any())
			->method('getDebugMode')
			->will($this->returnValue(false));

		$mksanitizedparameters::staticExpects($this->never())
			->method('getLogger');

		$mksanitizedparameters::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesCallsLoggerCorrectIfLoggingEnabledAndValueChanged(){
		tx_mklib_tests_Util::setExtConfVar('logMode', 1, 'mksanitizedparameters');

		$arrayToSanitize = array (
		  'parameter' => '"test"'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_STRING),
		);

		$mksanitizedparameters = $this->getMockClass(
			'tx_mksanitizedparameters', array('getDebugMode', 'getLogger')
		);

		$mksanitizedparameters::staticExpects($this->any())
			->method('getDebugMode')
			->will($this->returnValue(false));

		$logger = $this->getMockClass('tx_rnbase_util_Logger', array('warn'));
		$logger::staticExpects($this->once())
			->method('warn')
			->with(
				$mksanitizedparameters::MESSAGE_VALUE_HAS_CHANGED,
				'mksanitizedparameters',
				array(
					'Parameter Name:'				=> 'parameter',
					'initialer Wert:' 				=> '"test"',
					'Wert nach Bereinigung:'		=> '&#34;test&#34;',
					'komplettes Parameter Array'	=> array ('parameter' => '&#34;test&#34;')
				)
			);

		$mksanitizedparameters::staticExpects($this->once())
			->method('getLogger')
			->will($this->returnValue($logger));

		$mksanitizedparameters::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesDoesNotCallDebuggerIfDebuggingNotEnabledAndValueNotChanged(){
		$arrayToSanitize = array (
		  'parameter' => 'test'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_STRING),
		);

		$mksanitizedparameters = $this->getMockClass(
			'tx_mksanitizedparameters', array('getDebugger','getDebugMode')
		);
		$mksanitizedparameters::staticExpects($this->never())
			->method('getDebugMode');

		$mksanitizedparameters::staticExpects($this->never())
			->method('getDebugger');

		$mksanitizedparameters::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesDoesNotCallDebuggerIfDebuggingNotEnabledAndValueChanged(){
		$arrayToSanitize = array (
		  'parameter' => '"test"'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_STRING),
		);

		$mksanitizedparameters = $this->getMockClass(
			'tx_mksanitizedparameters', array('getDebugger','getDebugMode')
		);
		$mksanitizedparameters::staticExpects($this->once())
			->method('getDebugMode')
			->will($this->returnValue(false));

		$mksanitizedparameters::staticExpects($this->never())
			->method('getDebugger');

		$mksanitizedparameters::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesDoesNotCallDebuggerIfDebuggingEnabledButValueNotChanged(){
		$arrayToSanitize = array (
		  'parameter' => 'test'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_STRING),
		);

		$mksanitizedparameters = $this->getMockClass(
			'tx_mksanitizedparameters', array('getDebugger','getDebugMode')
		);
		$mksanitizedparameters::staticExpects($this->never())
			->method('getDebugMode');

		$mksanitizedparameters::staticExpects($this->never())
			->method('getDebugger');

		$mksanitizedparameters::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesCallsDebuggerCorrectIfDebuggingEnabledAndValueChanged(){
		$arrayToSanitize = array (
		  'parameter' => '"test"'
		);
		$rules = array(
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(FILTER_SANITIZE_STRING),
		);

		$mksanitizedparameters = $this->getMockClass(
			'tx_mksanitizedparameters', array('getDebugger','getDebugMode')
		);
		$mksanitizedparameters::staticExpects($this->once())
			->method('getDebugMode')
			->will($this->returnValue(true));

		$debugger = $this->getMockClass('tx_rnbase_util_Debug', array('debug'));
		$debugger::staticExpects($this->once())
			->method('debug')
			->with(
				array(
					array(
						'Parameter Name:'				=> 'parameter',
						'initialer Wert:' 				=> '"test"',
						'Wert nach Bereinigung:'		=> '&#34;test&#34;',
						'komplettes Parameter Array'	=> array ('parameter' => '&#34;test&#34;')
					)
				),
				$mksanitizedparameters::MESSAGE_VALUE_HAS_CHANGED
			);

		$mksanitizedparameters::staticExpects($this->once())
			->method('getDebugger')
			->will($this->returnValue($debugger));

		$mksanitizedparameters::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);
	}


	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesPrefersSpecialRulesOverCommonRules(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> '"1testValue"'
		);
		$rules = array(
			'parameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT,
			tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
				'parameterNameToBeSanitized'	=> FILTER_SANITIZE_STRING
			),
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(
				'filter'    => FILTER_CALLBACK,
               	'options' 	=> array(
               		'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
				)
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array('parameterNameToBeSanitized' 	=> '1'),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesPrefersCommonRulesOverDefaultRules(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> '"1testValue"'
		);
		$rules = array(
			'anotherParameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT,
			tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
				'parameterNameToBeSanitized'	=> FILTER_SANITIZE_STRING
			),
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(
				'filter'    => FILTER_CALLBACK,
               	'options' 	=> array(
               		'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
				)
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array('parameterNameToBeSanitized' 	=> '&#34;1testValue&#34;'),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesUsesDefaultRulesIfNoSpecialsOrCommons(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> '"1testValue"'
		);
		$rules = array(
			'anotherParameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT,
			tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
				'anotherParameterNameToBeSanitized'	=> FILTER_SANITIZE_STRING
			),
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(
				'filter'    => FILTER_CALLBACK,
               	'options' 	=> array(
               		'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
				)
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array('parameterNameToBeSanitized' 	=> 'testValue'),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesPrefersCommonRulesOverDefaultRulesWhenParameterNameInSubArray(){
		$arrayToSanitize = array(
			'myExt' => array(
				'parameterNameToBeSanitized' 	=> '"1testValue"'
			)
		);
		$rules = array(
			'anotherParameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT,
			tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
				'parameterNameToBeSanitized'	=> FILTER_SANITIZE_STRING
			),
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> array(
				'filter'    => FILTER_CALLBACK,
               	'options' 	=> array(
               		'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
				)
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array('myExt' => array('parameterNameToBeSanitized' 	=> '&#34;1testValue&#34;')),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesUsesCommonRulesInSubArrayEvenIfCommonRulesInMainArray(){
		$arrayToSanitize = array(
			'myExt' => array(
				'parameterNameToBeSanitized' 	=> '"1testValue"'
			)
		);
		$rules = array(
			'myExt'	=> array(
				tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
					'parameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT
				)
			),
			tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
				'parameterNameToBeSanitized'	=> FILTER_SANITIZE_STRING
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array('myExt' => array('parameterNameToBeSanitized' 	=> '1')),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesUsesDefaultRulesInSubArrayEvenIfDefaultRulesInMainArray(){
		$arrayToSanitize = array(
			'myExt' => array(
				'parameterNameToBeSanitized' 	=> '"1testValue"'
			)
		);
		$rules = array(
			'myExt'	=> array(
				tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY =>  FILTER_SANITIZE_NUMBER_INT
			),
			tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY	=> FILTER_SANITIZE_STRING
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array('myExt' => array('parameterNameToBeSanitized' 	=> '1')),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	* @group unit
	*/
	public function testSanitizeArrayByRulesMergesAndOverwritesCommonConfigIntoSubsequentLevels(){
		$arrayToSanitize = array(
			'myExt' => array(
				'parameterNameToBeSanitized' 		=> '"1testValue"',
				'anotherParameterNameToBeSanitized' => '"1testValue"'
			)
		);
		$rules = array(
			'myExt'	=> array(
				tx_mksanitizedparameters_Rules::COMMON_RULES_KEY =>  array(
					'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT
				)
			),
			tx_mksanitizedparameters_Rules::COMMON_RULES_KEY =>  array(
				'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_STRING
			)
		);
		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
			$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array(
				'myExt' => array(
					'parameterNameToBeSanitized' 		=> '1',
					'anotherParameterNameToBeSanitized' => '&#34;1testValue&#34;'
				)
			),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @group unit
	 */
	public function testSanitizeArrayByRulesWithRulesForSubArrayButSubArrayParameterItSelfIsGivenCastsFilterArrayConfigToIntegerResultingInEmptiedValue(){
		$arrayToSanitize = array(
			'myExt' => 'test'
		);
		$rules = array(
			'myExt'	=> array(
				'mySubParameter' => array(
					'filter'    => FILTER_CALLBACK,
					'options' 	=> array(
						'doesNotMatter','doesNotMatterToo'
					)
				),
			)
		);

		$mainClass = $this->getMainClassMockWithoutDebugMode();
		$sanitizedArray = $mainClass::sanitizeArrayByRules(
				$arrayToSanitize, $rules
		);

		$this->assertEquals(
			array('myExt' => ''),
			$sanitizedArray,
			'The array wasn\'t sanitized correct!'
		);
	}

	/**
	 * @return tx_mksanitizedparameters
	 */
	private function getMainClassMockWithoutDebugMode() {
		$mksanitizedparameters = $this->getMockClass(
			'tx_mksanitizedparameters', array('getDebugMode')
		);

		$mksanitizedparameters::staticExpects($this->any())
			->method('getDebugMode')
			->will($this->returnValue(false));

		return $mksanitizedparameters;
	}
}