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

//wir brauchen einen eigenen Hook damit der Debug Mode überschrieben wird
require_once tx_rnbase_util_Extensions::extPath('mksanitizedparameters', 'tests/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php');
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/template.php']['preStartPageHook']['mksanitizedparameters'] =
    'EXT:mksanitizedparameters/tests/hooks/class.tx_mksanitizedparameters_hooks_PreprocessTypo3Requests.php:&tx_mksanitizedparameters_tests_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays';

/**
 * @author Hannes Bochmann <dev@dmk-ebusiness.de>
 */
class tx_mksanitizedparameters_hooks_PreprocessTypo3Requests_testcase extends tx_rnbase_tests_BaseTestCase
{
    private $storedExtConfig;

    /**
     * (non-PHPdoc).
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->storedExtConfig =
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mksanitizedparameters'];
        $this->deactivateStealthMode($this->storedExtConfig);

        $rulesForBackend = array(
            'testParameter' => FILTER_SANITIZE_NUMBER_INT,
        );
        tx_mksanitizedparameters_Rules::addRulesForBackend($rulesForBackend);

        \DMK\Mklib\Utility\Tests::disableDevlog();
        \DMK\Mklib\Utility\Tests::storeExtConf('mksanitizedparameters');
        \DMK\Mklib\Utility\Tests::setExtConfVar('debugMode', 0, 'mksanitizedparameters');
        \DMK\Mklib\Utility\Tests::setExtConfVar('logMode', 0, 'mksanitizedparameters');

        $GLOBALS['TBE_TEMPLATE'] = tx_rnbase::makeInstance('TYPO3\CMS\Backend\Template\DocumentTemplate');
        /*
         * warning "Cannot modify header information" abfangen.
         *
         * Einige Tests lassen sich leider nicht ausführen:
         * "Cannot modify header information - headers already sent by"
         * Diese wird durch durch typo3\template.php Line: 738 zu ausgelöst
         * Ab Typo3 6.1 laufend die Tests auch auf der CLI nicht.
         * Eigentlich gibt es dafür die runInSeparateProcess Anotation,
         * Allerdings funktioniert diese bei Typo3 nicht, wenn versucht wird
         * die GLOBALS in den anderen Prozess zu übertragen.
         * Ein Deaktivierend er übertragung führt dazu,
         * das Typo3 nicht initialisiert ist.
         *
         * Wir gehen also erst mal den Weg, den Fehler abzufangen.
         */
        set_error_handler(array(__CLASS__, 'errorHandler'), E_WARNING);
    }

    /**
     * @param string $serializedExtConfig
     */
    private function deactivateStealthMode($serializedExtConfig)
    {
        $extConfig =
            unserialize($serializedExtConfig);

        if (!is_array($extConfig)) {
            $extConfig = array();
        }
        $extConfig['stealthMode'] = 0;
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mksanitizedparameters'] = serialize($extConfig);
    }

    /**
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @param array  $errcontext
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $ignoreMsg = array(
            'Cannot modify header information - headers already sent by',
        );
        foreach ($ignoreMsg as $msg) {
            if ((is_string($ignoreMsg) || is_numeric($ignoreMsg)) && false !== strpos($errstr, $ignoreMsg)) {
                // Don't execute PHP internal error handler
                return false;
            }
        }

        return null;
    }

    /**
     * (non-PHPdoc).
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mksanitizedparameters'] =
            $this->storedExtConfig;

        unset($_REQUEST['testParameter']);
        unset($_POST['testParameter']);
        unset($_GET['testParameter']);

        \DMK\Mklib\Utility\Tests::restoreExtConf('mksanitizedparameters');

        // error handler zurücksetzen
        restore_error_handler();
    }

    /**
     * @group integration
     */
    public function testHookIsCalledInBackendAndSanitizesRequestPostAndGetGlobals()
    {
        $_POST['testParameter'] = '2WithString';
        $_GET['testParameter'] = '2WithString';

        /* @var $template \TYPO3\CMS\Backend\Template\DocumentTemplate */
        $template = tx_rnbase::makeInstance(tx_rnbase_util_Typo3Classes::getDocumentTemplateClass());
        $template->startPage('testPage');

        $this->assertEquals(
            2,
            $_POST['testParameter'],
            'Parameter nicht bereinigt'
        );
        $this->assertEquals(
            2,
            $_GET['testParameter'],
            'Parameter nicht bereinigt'
        );
    }

    /**
     * we can't check if the hook is called correctly in FE as
     * including PATH_typo3.'sysext/cms/tslib/index_ts.php'
     * results in an fatal error. so we test at least if the hook
     * is offered in PATH_typo3.'sysext/cms/tslib/index_ts.php'
     * and if the hook is configured correctly.
     *
     * @group integration
     */
    public function testHookInFrontendIsAvailableAndConfigured()
    {
        $indexTs = file_get_contents(
            \Sys25\RnBase\Utility\Environment::getPublicTypo3Path().'sysext/frontend/Classes/Http/RequestHandler.php'
        );
        $callHookLine = strstr(
            $indexTs,
            'foreach ($GLOBALS[\'TYPO3_CONF_VARS\'][\'SC_OPTIONS\'][\'tslib/index_ts.php\'][\'preprocessRequest\'] as $hookFunction) {'
        );

        $this->assertNotEmpty(
            $callHookLine,
            'The line calling the FE hook wasn\'t found in '.\Sys25\RnBase\Utility\Environment::getPublicTypo3Path().
            'sysext/cms/tslib/index_ts.php'
        );

        $this->assertTrue(
            in_array(
                'tx_mksanitizedparameters_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays',
                $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['determineId-PostProc']
            ),
            'The FE Hook is not configured'
        );
    }
}
