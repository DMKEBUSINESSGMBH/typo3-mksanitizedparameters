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
	public function testSanitizeArrayByConfigReturnsUntouchedArrayIfConfigEmpty(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' => 'testValue'
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
			$arrayToSanitize, array()
		);
		
		$this->assertSame(
			$arrayToSanitize, $sanitizedArray, 'The array was touched!'
		);
	}
	
	/**
	 * @group unit
	 */
	public function testSanitizeArrayByConfigReturnsUntouchedArrayWithoutConfigForGivenParameter(){
		$arrayToSanitize = array(
			'parameterNameWithoutConfig' => 'testValue'
		);
		$config = array(
			'unexistentParameter'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
			$arrayToSanitize, $config
		);
		
		$this->assertSame(
			$arrayToSanitize, $sanitizedArray, 'The array was touched!'
		);
	}
	
	/**
	 * @group unit
	 */
	public function testSanitizeArrayByConfigWorksCorrectWithUnconfiguredValuesButDefaultConfig(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> '1testValue'
		);
		$config = array(
			'default'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
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
	public function testSanitizeArrayByConfigWorksCorrectWithFlatArrayAndSingleFilterConfig(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> '1testValue'
		);
		$config = array(
			'parameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
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
	public function testSanitizeArrayByConfigWorksCorrectWithFlatArrayAndFilterConfigAsArray(){
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
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
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
	public function testSanitizeArrayByConfigWorksCorrectWithUnconfiguredValuesAndNoDefaultConfig(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 		=> '1testValue',
			'parameterNameNotToBeSanitized' 	=> '1testValue',
		);
		$config = array(
			'parameterNameToBeSanitized'	=> FILTER_SANITIZE_NUMBER_INT
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
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
	public function testSanitizeArrayByConfigWorksCorrectWithMultiDimensionalArray(){
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
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
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
	public function testSanitizeArrayByConfigWorksCorrectWithMultiDimensionalArrayAndDefaultConfigOnlyForSubArray(){
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
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
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
	public function testSanitizeArrayByConfigWorksCorrectWithSeveralConfiguredFiltersInVersion1(){
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
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
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
	public function testSanitizeArrayByConfigWorksCorrectWithSeveralConfiguredFiltersInVersion2(){
		$arrayToSanitize = array(
			'parameterNameToBeSanitized' 	=> 
			"<span>Is your name O'reilly & are sure about that?</span>",
		);
		$config = array(
			'parameterNameToBeSanitized'	=> array(
				FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES
			)
		);
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
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
	public function testSanitizeArrayByConfigWorksCorrectWithCustomFilter(){
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
		$sanitizedArray = tx_mksanitizedparameters::sanitizeArrayByConfig(
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
}