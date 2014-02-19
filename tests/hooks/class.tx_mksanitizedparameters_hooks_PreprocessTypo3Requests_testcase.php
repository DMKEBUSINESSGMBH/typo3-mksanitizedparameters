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

//wir brauchen eine XClass damit der Debug Mode überschrieben wird
global $TYPO3_CONF_VARS;
$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mksanitizedparameters/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php'] = 
	t3lib_extMgm::extPath('mksanitizedparameters') . 'tests/hooks/class.ux_tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php';

tx_rnbase::load('tx_mksanitizedparameters');
tx_rnbase::load('tx_mksanitizedparameters_Rules');
tx_rnbase::load('tx_mklib_tests_Util');
	
//otherwise we get an output already started error when the test is excuted
//via CLI. caused by $template->startPage('testPage');
//@FIXME find a betty way to avoid the problem
if((defined('TYPO3_cliMode') && TYPO3_cliMode)) {
	ob_start();
}

/**
 * @package TYPO3
 * @subpackage tx_mksanitizedparameters
 * @author Hannes Bochmann <hannes.bochmann@das-mediekombinat.de>
 */
class tx_mksanitizedparameters_hooks_PreprocessTypo3Requests_testcase extends tx_phpunit_testcase {
	
	private $storedExtConfig;
	
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		$this->storedExtConfig =  
			$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mksanitizedparameters'];
		$this->deactivateStealthMode($this->storedExtConfig);
		
		$rulesForBackend = array(
			'testParameter' => FILTER_SANITIZE_NUMBER_INT
		);
		tx_mksanitizedparameters_Rules::addRulesForBackend($rulesForBackend);
		require_once PATH_site.TYPO3_mainDir.'template.php';
		
		tx_mklib_tests_Util::disableDevlog();
		tx_mklib_tests_Util::storeExtConf('mksanitizedparameters');
		tx_mklib_tests_Util::setExtConfVar('debugMode', 0, 'mksanitizedparameters');
		tx_mklib_tests_Util::setExtConfVar('logMode', 0, 'mksanitizedparameters');
		
		if(tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
			$GLOBALS['TBE_TEMPLATE'] = 
				tx_rnbase::makeInstance('TYPO3\CMS\Backend\Template\DocumentTemplate');
		}
	}
	
	/**
	 * @param string $serializedExtConfig
	 * 
	 * @return void
	 */
	private function deactivateStealthMode($serializedExtConfig) {
		$extConfig = 
			unserialize($serializedExtConfig);
		
		if(!is_array($extConfig)) {
			$extConfig = array();
		}
		$extConfig['stealthMode'] = 0;
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mksanitizedparameters'] = serialize($extConfig);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown() {
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mksanitizedparameters'] =
			$this->storedExtConfig;
		
		unset($_REQUEST['testParameter']);
		unset($_POST['testParameter']);
		unset($_GET['testParameter']);
		
		tx_mklib_tests_Util::restoreExtConf('mksanitizedparameters');
	}
	
	/**
	 * @group integration
	 * 
	 * Dieser Test lässt sich leider nicht im BE ausführen wegen
	 * "Cannot modify header information - headers already sent by" Meldung 
	 * diese kommt durch typo3\template.php Line: 738 zu Stande
	 * auf CLI läuft alles
	 */
	public function testHookIsCalledInBackendAndSanitizesRequestPostAndGetGlobals(){
		$_COOKIE['testParameter'] = '2WithString';
		$_POST['testParameter'] = '2WithString';
		$_GET['testParameter'] = '2WithString';
		
		$template = tx_rnbase::makeInstance('template');
		$template->startPage('testPage');
		ob_end_flush();
		
		$this->assertEquals(
			2,$_COOKIE['testParameter'], 'Parameter nicht bereinigt'
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
	public function testHookInFrontendIsAvailableAndConfigured(){
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