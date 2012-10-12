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
	
//otherwise we get an output already started error when the test is excuted
//via CLI
ob_start();

/**
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <hannes.bochmann@das-mediekombinat.de>
 */
class tx_mksanitizedparameters_hooks_PreprocessTypo3Requests_testcase extends tx_phpunit_testcase {
	
	protected function setUp() {
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mksanitizedparameters']['parameterRules']['BE']['testParameter'] = 
			FILTER_SANITIZE_NUMBER_INT;
		require_once PATH_site.TYPO3_mainDir.'template.php';
	}
	
	protected function tearDown() {
		unset(
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mksanitizedparameters']['parameterRules']['BE']['testParameter']
		);
		unset($_REQUEST['testParameter']);
		unset($_POST['testParameter']);
		unset($_GET['testParameter']);
	}
	
	/**
	 * @group integration
	 */
	public function testHookIsCalledInBackendAndSanitizesRequestPostAndGetGlobals(){
		$_REQUEST['testParameter'] = '2WithString';
		$_POST['testParameter'] = '2WithString';
		$_GET['testParameter'] = '2WithString';
		
		$template = tx_rnbase::makeInstance('template');
		$template->startPage('testPage');
		ob_end_flush();
		
		$this->assertEquals(
			2,$_REQUEST['testParameter'], 'Parameter nicht bereinigt'
		);
		$this->assertEquals(
			2,$_POST['testParameter'], 'Parameter nicht bereinigt'
		);
		$this->assertEquals(
			2,$_GET['testParameter'], 'Parameter nicht bereinigt'
		);
	}
	
	/**
	 * we can't check if the hook is called correctly in FE as 
	 * including PATH_site.TYPO3_mainDir.'sysext/cms/tslib/index_ts.php'
	 * results in an fatal error. so we test at least if the hook
	 * is offered in PATH_site.TYPO3_mainDir.'sysext/cms/tslib/index_ts.php'
	 * and if the hook is configured correctly.
	 * 
	 * @group integration
	 */
	public function testHookIsCalledInFrontendAndSanitizesRequestGlobals(){
		$indexTs = 
			file_get_contents(PATH_site.TYPO3_mainDir.'sysext/cms/tslib/index_ts.php');

		$callHookLine = 
			strstr($indexTs, 'foreach ($TYPO3_CONF_VARS[\'SC_OPTIONS\'][\'tslib/index_ts.php\'][\'preprocessRequest\'] as $hookFunction) {');
			
		$this->assertNotEmpty($callHookLine, 'The line calling the FE hook wasn\'t found in '.PATH_site.TYPO3_mainDir.'sysext/cms/tslib/index_ts.php');
		
		$this->assertTrue(
			in_array(
				'EXT:mksanitizedparameters/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php:tx_mksanitizedparameters_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays', 
				$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest']
			),
			'The FE Hook is not configured'
		);
	}
}