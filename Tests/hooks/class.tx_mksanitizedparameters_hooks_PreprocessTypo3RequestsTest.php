<?php
/**
 *  Copyright notice.
 *
 *  (c) 2020 DMK E-Business GmbH <dev@dmk-ebusiness.de>
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
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mksanitizedparameters_hooks_PreprocessTypo3RequestsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * (non-PHPdoc).
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $config = \DMK\MkSanitizedParameters\Factory::getConfiguration();
        if ($config->isStealthMode()) {
            $reflector = new ReflectionClass(get_class($config));
            $property = $reflector->getProperty('extensionConfiguration');
            $property->setAccessible(true);
            $property->setValue(
                $config,
                array_merge(
                    $property->getValue($config),
                    ['stealthMode' => 0]
                )
            );
        }

        tx_mksanitizedparameters_Rules::addRulesForBackend([
            'testParameter' => FILTER_SANITIZE_NUMBER_INT,
        ]);
    }

    /**
     * @group integration
     */
    public function testHookIsCalledInBackendAndSanitizesRequestPostAndGetGlobals()
    {
        $_POST['testParameter'] = '2WithString';
        $_GET['testParameter'] = '2WithString';

        $params = [];
        \TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction(
            'tx_mksanitizedparameters_hooks_PreprocessTypo3Requests->sanitizeGlobalInputArrays',
            $params,
            $this
        );

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
}
