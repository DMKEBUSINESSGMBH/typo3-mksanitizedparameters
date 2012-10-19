<?php
/**
 *
 *  Copyright notice
 *
 *  (c) 2012 das MedienKombinat GmbH <kontakt@das-medienkombinat.de>
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
	
/**
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <hannes.bochmann@das-mediekombinat.de>
 */
class tx_mksanitizedparameters_testcase extends tx_phpunit_testcase {
	
	/**
	 * @group unit
	 */
	public function testSanitizeArrayByConfigurationReturnsUntouchedArrayIfConfigEmpty(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' => 'testValue'
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, array()
		);
		
		$this->assertSame(
			$arrayToSanitize, $sanitizedArray, 'The array was touched!'
		);
	}
	
	/**
	 * @group unit
	 */
	public function testSanitizeArrayByConfigurationReturnsUntouchedArrayWithoutConfigForGivenParameter(){
		$arrayToSanitize = array(
			'parameterNameWithoutConfig' => 'testValue'
		);
		$config = array(
			'unexistentParameter'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
		);
		
		$this->assertSame(
			$arrayToSanitize, $sanitizedArray, 'The array was touched!'
		);
	}
	
	/**
	 * @group unit
	 */
	public function testSanitizeArrayByConfigurationWorksCorrectWithUnconfiguredValuesButDefaultConfig(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> '1testValue'
		);
		$config = array(
			'default'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
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
	public function testSanitizeArrayByConfigurationWorksCorrectWithFlatArrayAndSingleFilterConfig(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> '1testValue'
		);
		$config = array(
			'parameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
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
	public function testSanitizeArrayByConfigurationWorksCorrectWithFlatArrayAndFilterConfigAsArray(){
		$arrayToSanitize = array(
			'parameterInRange' 		=> '<span>me&you</span>',
			'parameterOutOfRange' 	=> '<span>me&you</span>'
		);
		$config = array(
			'parameterInRange'	=> array(
				'filter'    => FILTER_SANITIZE_STRING,
			),
			'parameterOutOfRange'	=> array(
				'filter'    => FILTER_SANITIZE_STRING,
                'flags'   	=> FILTER_FLAG_ENCODE_AMP
			)
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
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
	public function testSanitizeArrayByConfigurationWorksCorrectWithUnconfiguredValuesAndNoDefaultConfig(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 		=> '1testValue',
			'parameterNameNotToBeSanitized' 	=> '1testValue',
		);
		$config = array(
			'parameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
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
	public function testSanitizeArrayByConfigurationWorksCorrectWithMultiDimensionalArray(){
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
		$config = array(
			'default' => FILTER_SANITIZE_ENCODED,
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
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
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
	public function testSanitizeArrayByConfigurationWorksCorrectWithMultiDimensionalArrayAndDefaultConfigOnlyForSubArray(){
		$arrayToSanitize = array(
			'firstExtensionQualifier'	=> array(
				'parameterNameToBeSanitizedByDefault' 	=> '1testValue',
			),
			'parameterNameToBeSanitizedByDefault' 		=> 'libgd<script>'
		);
		$config = array(
			'default' => FILTER_SANITIZE_ENCODED,
			'firstExtensionQualifier'	=> array(
				'default' => FILTER_SANITIZE_NUMBER_INT
			),
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
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
	public function testSanitizeArrayByConfigurationWorksCorrectWithSeveralConfiguredFiltersInVersion1(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> 
			"<span>Is your name O'reilly & are sure about that?</span>",
		);
		$config = array(
			'parameterNameToBeSanitized'	=> array(
				'filter'    => array(
					FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
				),
                'flags'   	=> FILTER_FLAG_ENCODE_AMP
			)
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
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
	public function testSanitizeArrayByConfigurationWorksCorrectWithSeveralConfiguredFiltersInVersion2(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> 
			"<span>Is your name O'reilly & are sure about that?</span>",
		);
		$config = array(
			'parameterNameToBeSanitized'	=> array(
				FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
			)
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
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
	public function testSanitizeArrayByConfigurationWorksCorrectWithCustomFilter(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> 
			"abc123",
		);
		$config = array(
			'parameterNameToBeSanitized'	=> array(
				'filter'    => FILTER_CALLBACK,
               	'options' 	=> array(
               		'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
				)
			)
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
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
	public function testSanitizeArrayByConfigurationWithTypicalCaretakerRequest(){
		$arrayToSanitize = array (
		  'st' => '1350376881:2f69b091adea4517b68d0d62ac1d2a792f2535462ae5',
		  'd' => '{\\"encrypted\\":\\"Fgi8\\\\/oEXD0CnEQTcGtmfA1Phu7RBD+gZwY2ymllDJ9umVv4USQ\\\\/dGroux\\\\/OrrERyCo7t8DiRoaDW58P6UetoOgu9JLtol320HQVWzVPc\\\\/NObedgTZEo\\\\/He9NjKTpMLq32R77WC5WMblPim4l2Yuq2To+llrhYTgbbXWkgKi9PgQ=:9yxcT\\\\/dymal1z1d1zDVazjnkwSvLovTN5Sj1toYjsley3V0R9u62c+0mK2nf1ogNL3v08Tp7tIuxXU9qeJZNGt95zoGI\\\\/Ntnl1\\\\/OdJiPvHcdt0SIlXk3CAXOvxO2N8gEr3CncX+22RWt6AqtLKOLqHrMerXwShz5AUBfy8SlPOILOIhMvTRr8PeIyxvon7wOBUxXwj21dVL3bYMirCjUpLyvQ68wdTOhmZvEPOq5psxrpIfkGEIgeOpsXTql1ePkRXC6\\\\/5nD\\\\/nAmuTqw\\\\/fRBflgQW8SgWjLwctfjD1vH9meiO6wibuDEKc3iKp2vvFXWEHw\\\\/5CPAia1PPZpmRDrqNpsEI6A00W3WJhpqwXFlR06g1pUyPPt8MrIbr7Ioq+l+PzRpQCbeT0is6F87b2CRCj8jmEJ9F80LCr62n1hiKymwzx2u4+vYaRFt7zaU2pagaFDj3kkIhLNwdQmMsnK\\\\/U9Z8z53D7YiWhbr04rgE2EP8JCN022WPY2hfejXKKSi3RE0TTvo8ixD\\\\/Jh0Lbu3rK0NwY\\\\/6Tnn4dLDzLe0KXvbeMM3+JoNm6AwnL\\\\/zGkZ7IEb8Gol+H7MeEHt1DLmLXmWRuhOA+Co3k\\\\/hw4qMOEQRg+PLNy0AlsWouDCGhnoteWXZPnA4MRx6kDB+v+NhBk2KHx3UeXXP2vXPNhmXzD8iQ8PybmYN45WDmsftK+cHl1OAAC+wO2Z2XCnVTs36c525d6FuX0SlOKEZ\\\\/A6kY7ERTnJ2I4g1lYHAM\\\\/XB19lRfuYGJpJ3mowGL1T6POFqwf6M8Dw8V6LQ9X2QCOQUrO9woq2Jz4LWRxGX0a6mZbvrtpm07tDQeX34lYAEhlL\\\\/nnoqqbI5\\\\/BB03gz4bknhw2hG5K0XoQErEwXEAUugViW0RKKL5O11iRQBXGToZogkecXj1PfCJhLI1UZyEQRmpOP6+19r8sQ+JrJf2GDRHAzqWU2VUHdlNlmd4u22FPnOAGSs2gMDNqBpxs1IA==\\"}',
		  's' => 'acFNX7gVY8wm0mLQbxRaVr8nrnRbPywGb3tjkspp0HC77io/T94qEQbPHMePi2xpNtfpJut9bt2USUBfkCXmc/wKk1Unk7WX7XMoohSuI1BahtNV4DRfGKKpUJV6s+5cD7IET7IFjVLm/wmxs+hl/1Ve1MIjZe2L3VCs4VqmBsg=',
		);
		$config = array(
			'default'	=> array(FILTER_SANITIZE_URL),
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfiguration(
			$arrayToSanitize, $config
		);
		
		$this->assertEquals(
			$arrayToSanitize,
			$sanitizedArray, 
			'The array wasn\'t sanitized correct!'
		);
	}
}