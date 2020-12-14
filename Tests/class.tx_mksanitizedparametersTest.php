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
class tx_mksanitizedparametersTest extends \PHPUnit\Framework\TestCase
{
    protected $backup = [];

    protected function setUp()
    {
        $this->backup = [
            '_SERVER' => $_SERVER,
            'TYPO3_CONF_VARS' => $GLOBALS['TYPO3_CONF_VARS'],
        ];
        $_SERVER['REMOTE_ADDR'] = 'testip';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'] = 'testip';

        $this->setExtConf(
            [
                'debugMode' => 0,
                'logMode' => 0,
            ]
        );
    }

    protected function tearDown()
    {
        $_SERVER = $this->backup['_SERVER'];
        $GLOBALS['TYPO3_CONF_VARS'] = $this->backup['TYPO3_CONF_VARS'];
    }

    protected function setExtConf(array $extConf)
    {
        $config = \DMK\MkSanitizedParameters\Factory::getConfiguration();
        // force ext conf creation
        $config->isStealthMode();
        // now override the extconf array property
        $reflector = new ReflectionClass(get_class($config));
        $property = $reflector->getProperty('extensionConfiguration');
        $property->setAccessible(true);
        $property->setValue(
            $config,
            array_merge(
                $property->getValue($config),
                $extConf
            )
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesReturnsUntouchedArrayIfRulesEmpty()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => 'testValue',
        ];

        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            []
        );

        $this->assertSame(
            $arrayToSanitize,
            $sanitizedArray,
            'The array was touched!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesReturnsUntouchedArrayWithoutRulesForGivenParameter()
    {
        $arrayToSanitize = [
            'parameterNameWithoutRules' => 'testValue',
        ];
        $rules = [
            'unexistentParameter' => FILTER_SANITIZE_NUMBER_INT,
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertSame(
            $arrayToSanitize,
            $sanitizedArray,
            'The array was touched!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWorksCorrectWithUnconfiguredValuesButDefaultRules()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => '1testValue',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT,
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            ['parameterNameToBeSanitized' => '1'],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWorksCorrectWithFlatArrayAndSingleFilterConfig()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => '1testValue',
        ];
        $rules = [
            'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            ['parameterNameToBeSanitized' => '1'],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWorksCorrectWithFlatArrayAndFilterConfigAsArray()
    {
        $arrayToSanitize = [
            'parameterInRange' => '<span>me&you</span>',
            'parameterOutOfRange' => '<span>me&you</span>',
        ];
        $rules = [
            'parameterInRange' => [
                'filter' => FILTER_SANITIZE_STRING,
            ],
            'parameterOutOfRange' => [
                'filter' => FILTER_SANITIZE_STRING,
                'flags' => FILTER_FLAG_ENCODE_AMP,
            ],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            [
                'parameterInRange' => 'me&you',
                'parameterOutOfRange' => 'me&#38;you',
            ],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWorksCorrectWithUnconfiguredValuesAndNoDefaultRules()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => '1testValue',
            'parameterNameNotToBeSanitized' => '1testValue',
        ];
        $rules = [
            'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            [
                'parameterNameToBeSanitized' => '1',
                'parameterNameNotToBeSanitized' => '1testValue',
            ],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWorksCorrectWithMultiDimensionalArray()
    {
        $arrayToSanitize = [
            'firstExtensionQualifier' => [
                'parameterNameToBeSanitized' => '1testValue',
                'parameterNameToBeSanitizedByDefault' => 'libgd<script>',
            ],
            'secondExtensionQualifier' => [
                'subArray' => [
                    'parameterNameToBeSanitized' => '<span>me&you</span>',
                ],
            ],
            'parameterNameToBeSanitizedByDefault' => 'libgd<script>',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_ENCODED,
            'firstExtensionQualifier' => [
                'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
            ],
            'secondExtensionQualifier' => [
                'subArray' => [
                    'parameterNameToBeSanitized' => [
                        'filter' => FILTER_SANITIZE_STRING,
                        'flags' => FILTER_FLAG_ENCODE_AMP,
                    ],
                ],
            ],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $expectedArray = [
            'firstExtensionQualifier' => [
                'parameterNameToBeSanitized' => 1,
                'parameterNameToBeSanitizedByDefault' => 'libgd%3Cscript%3E',
            ],
            'secondExtensionQualifier' => [
                'subArray' => [
                    'parameterNameToBeSanitized' => 'me&#38;you',
                ],
            ],
            'parameterNameToBeSanitizedByDefault' => 'libgd%3Cscript%3E',
        ];

        $this->assertEquals(
            $expectedArray,
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWorksCorrectWithMultiDimensionalArrayAndDefaultRulesOnlyForSubArray()
    {
        $arrayToSanitize = [
            'firstExtensionQualifier' => [
                'parameterNameToBeSanitizedByDefault' => '1testValue',
            ],
            'parameterNameToBeSanitizedByDefault' => 'libgd<script>',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_ENCODED,
            'firstExtensionQualifier' => [
                tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT,
            ],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $expectedArray = [
            'firstExtensionQualifier' => [
                'parameterNameToBeSanitizedByDefault' => 1,
            ],
            'parameterNameToBeSanitizedByDefault' => 'libgd%3Cscript%3E',
        ];

        $this->assertEquals(
            $expectedArray,
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWorksCorrectWithSeveralConfiguredFiltersAsFilterArray()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => "<span>Is your name O'reilly & are sure about that?</span>",
        ];
        $rules = [
            'parameterNameToBeSanitized' => [
                'filter' => [
                    FILTER_SANITIZE_STRING, FILTER_SANITIZE_MAGIC_QUOTES,
                ],
                'flags' => FILTER_FLAG_ENCODE_AMP,
            ],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            [
                'parameterNameToBeSanitized' => 'Is your name O&#39;reilly &#38; are sure about that?',
            ],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWorksCorrectWithSeveralConfiguredFiltersAsList()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => "<span>Is your name O'reilly & are sure about that?</span>",
        ];
        $rules = [
            'parameterNameToBeSanitized' => [
                FILTER_SANITIZE_STRING,
                FILTER_SANITIZE_MAGIC_QUOTES,
            ],
        ];

        /* @var $mksanitizedparameters tx_mksanitizedparameters */
        $mksanitizedparameters = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            [
                'parameterNameToBeSanitized' => 'Is your name O&#39;reilly & are sure about that?',
            ],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWorksCorrectWithCustomFilter()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => 'abc123',
        ];
        $rules = [
            'parameterNameToBeSanitized' => [
                'filter' => FILTER_CALLBACK,
                   'options' => [
                       'tx_mksanitizedparameters_sanitizer_Alpha', 'sanitizeValue',
                ],
            ],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            [
                'parameterNameToBeSanitized' => 'abc',
            ],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWithTypicalCaretakerRequest()
    {
        $arrayToSanitize = [
          'st' => '1350376881:2f69b091adea4517b68d0d62ac1d2a792f2535462ae5',
          'd' => '{\\"encrypted\\":\\"Fgi8\\\\/oEXD0CnEQTcGtmfA1Phu7RBD+gZwY2ymllDJ9umVv4USQ\\\\/dGroux\\\\/OrrERyCo7t8DiRoaDW58P6UetoOgu9JLtol320HQVWzVPc\\\\/NObedgTZEo\\\\/He9NjKTpMLq32R77WC5WMblPim4l2Yuq2To+llrhYTgbbXWkgKi9PgQ=:9yxcT\\\\/dymal1z1d1zDVazjnkwSvLovTN5Sj1toYjsley3V0R9u62c+0mK2nf1ogNL3v08Tp7tIuxXU9qeJZNGt95zoGI\\\\/Ntnl1\\\\/OdJiPvHcdt0SIlXk3CAXOvxO2N8gEr3CncX+22RWt6AqtLKOLqHrMerXwShz5AUBfy8SlPOILOIhMvTRr8PeIyxvon7wOBUxXwj21dVL3bYMirCjUpLyvQ68wdTOhmZvEPOq5psxrpIfkGEIgeOpsXTql1ePkRXC6\\\\/5nD\\\\/nAmuTqw\\\\/fRBflgQW8SgWjLwctfjD1vH9meiO6wibuDEKc3iKp2vvFXWEHw\\\\/5CPAia1PPZpmRDrqNpsEI6A00W3WJhpqwXFlR06g1pUyPPt8MrIbr7Ioq+l+PzRpQCbeT0is6F87b2CRCj8jmEJ9F80LCr62n1hiKymwzx2u4+vYaRFt7zaU2pagaFDj3kkIhLNwdQmMsnK\\\\/U9Z8z53D7YiWhbr04rgE2EP8JCN022WPY2hfejXKKSi3RE0TTvo8ixD\\\\/Jh0Lbu3rK0NwY\\\\/6Tnn4dLDzLe0KXvbeMM3+JoNm6AwnL\\\\/zGkZ7IEb8Gol+H7MeEHt1DLmLXmWRuhOA+Co3k\\\\/hw4qMOEQRg+PLNy0AlsWouDCGhnoteWXZPnA4MRx6kDB+v+NhBk2KHx3UeXXP2vXPNhmXzD8iQ8PybmYN45WDmsftK+cHl1OAAC+wO2Z2XCnVTs36c525d6FuX0SlOKEZ\\\\/A6kY7ERTnJ2I4g1lYHAM\\\\/XB19lRfuYGJpJ3mowGL1T6POFqwf6M8Dw8V6LQ9X2QCOQUrO9woq2Jz4LWRxGX0a6mZbvrtpm07tDQeX34lYAEhlL\\\\/nnoqqbI5\\\\/BB03gz4bknhw2hG5K0XoQErEwXEAUugViW0RKKL5O11iRQBXGToZogkecXj1PfCJhLI1UZyEQRmpOP6+19r8sQ+JrJf2GDRHAzqWU2VUHdlNlmd4u22FPnOAGSs2gMDNqBpxs1IA==\\"}',
          's' => 'acFNX7gVY8wm0mLQbxRaVr8nrnRbPywGb3tjkspp0HC77io/T94qEQbPHMePi2xpNtfpJut9bt2USUBfkCXmc/wKk1Unk7WX7XMoohSuI1BahtNV4DRfGKKpUJV6s+5cD7IET7IFjVLm/wmxs+hl/1Ve1MIjZe2L3VCs4VqmBsg=',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_URL],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
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
    public function testSanitizeArrayByRulesDoesNotCallLoggerIfLoggingNotEnabledAndValueNotChanged()
    {
        $arrayToSanitize = [
          'parameter' => 'test',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING],
        ];

        $mksanitizedparameters = $this->getMock('tx_mksanitizedparameters', ['getLogger']);

        $mksanitizedparameters->expects($this->never())
            ->method('getLogger')
            ->willReturn($this->createMock(\TYPO3\CMS\Core\Log\Logger::class));

        $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesDoesNotCallLoggerIfLoggingNotEnabledAndValueChanged()
    {
        $arrayToSanitize = [
          'parameter' => '"test"',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING],
        ];

        $mksanitizedparameters = $this->getMock(
            'tx_mksanitizedparameters',
            ['getDebugMode', 'getLogger']
        );

        $mksanitizedparameters->expects($this->any())
            ->method('getDebugMode')
            ->will($this->returnValue(false));

        $mksanitizedparameters->expects($this->never())
            ->method('getLogger');

        $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesDoesNotCallLoggerIfLoggingEnabledButValueNotChanged()
    {
        $arrayToSanitize = [
          'parameter' => 'test',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING],
        ];

        $mksanitizedparameters = $this->getMock(
            'tx_mksanitizedparameters',
            ['getDebugMode', 'getLogger']
        );

        $mksanitizedparameters->expects($this->any())
            ->method('getDebugMode')
            ->will($this->returnValue(false));

        $mksanitizedparameters->expects($this->never())
            ->method('getLogger');

        $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesDoesNotConsiderValueAsChangedIfHasWhitespaceAtBeginningOrEnd()
    {
        $arrayToSanitize = [
            'parameter' => ' test ',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING],
        ];

        $mksanitizedparameters = $this->getMock(
            'tx_mksanitizedparameters',
            ['getDebugMode', 'getLogger']
        );

        $mksanitizedparameters->expects($this->any())
            ->method('getDebugMode')
            ->will($this->returnValue(false));

        $mksanitizedparameters->expects($this->never())
            ->method('getLogger');

        $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesCallsLoggerCorrectIfLoggingEnabledAndValueChanged()
    {
        $this->setExtConf(
            [
                'debugMode' => 0,
                'logMode' => 1,
            ]
        );

        $arrayToSanitize = [
          'parameter' => '"test"',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING],
        ];

        /* @var $mainClass \tx_mksanitizedparameters*/
        $mksanitizedparameters = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $mksanitizedparameters = $this->getMock(
            'tx_mksanitizedparameters',
            ['getLogger']
        );

        $logger = $this->createMock(\TYPO3\CMS\Core\Log\Logger::class);
        $logger->expects($this->once())
            ->method('warning')
            ->with(
                $mksanitizedparameters::MESSAGE_VALUE_HAS_CHANGED,
                [
                    'Parameter Name:' => 'parameter',
                    'initialer Wert:' => '"test"',
                    'Wert nach Bereinigung:' => '&#34;test&#34;',
                    'komplettes Parameter Array' => ['parameter' => '&#34;test&#34;'],
                ]
            );

        $mksanitizedparameters->expects($this->once())
            ->method('getLogger')
            ->will($this->returnValue($logger));

        $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesDoesNotCallDebuggerIfDebuggingNotEnabledAndValueNotChanged()
    {
        $arrayToSanitize = [
          'parameter' => 'test',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING],
        ];

        $mksanitizedparameters = $this->getMock(
            'tx_mksanitizedparameters',
            ['getDebugger', 'getDebugMode']
        );
        $mksanitizedparameters->expects($this->never())
            ->method('getDebugMode');

        $mksanitizedparameters->expects($this->never())
            ->method('getDebugger');

        $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesDoesNotCallDebuggerIfDebuggingNotEnabledAndValueChanged()
    {
        $this->setExtConf(
            [
                'debugMode' => 0,
                'logMode' => 1,
            ]
        );

        $arrayToSanitize = [
          'parameter' => '"test"',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING],
        ];

        $mksanitizedparameters = $this->getMock(
            'tx_mksanitizedparameters',
            ['echoDebug']
        );

        $mksanitizedparameters->expects($this->never())
            ->method('echoDebug');

        $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesDoesNotCallDebuggerIfDebuggingEnabledButValueNotChanged()
    {
        $arrayToSanitize = [
          'parameter' => 'test',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING],
        ];

        $mksanitizedparameters = $this->getMock(
            'tx_mksanitizedparameters',
            ['getDebugger', 'getDebugMode']
        );
        $mksanitizedparameters->expects($this->never())
            ->method('getDebugMode');

        $mksanitizedparameters->expects($this->never())
            ->method('getDebugger');

        $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesCallsDebuggerCorrectIfDebuggingEnabledAndValueChanged()
    {
        $this->setExtConf(
            [
                'debugMode' => 1,
                'logMode' => 0,
            ]
        );

        $arrayToSanitize = [
          'parameter' => '"test"',
        ];
        $rules = [
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [FILTER_SANITIZE_STRING],
        ];

        $mksanitizedparameters = $this->getMock(
            'tx_mksanitizedparameters',
            ['echoDebug']
        );

        $mksanitizedparameters->expects($this->once())
            ->method('echoDebug')
            ->with(
                [
                    'Parameter Name:' => 'parameter',
                    'initialer Wert:' => '"test"',
                    'Wert nach Bereinigung:' => '&#34;test&#34;',
                    'komplettes Parameter Array' => ['parameter' => '&#34;test&#34;'],
                ]
            );

        $mksanitizedparameters->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesPrefersSpecialRulesOverCommonRules()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => '"1testValue"',
        ];
        $rules = [
            'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => [
                'parameterNameToBeSanitized' => FILTER_SANITIZE_STRING,
            ],
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [
                'filter' => FILTER_CALLBACK,
                   'options' => [
                       'tx_mksanitizedparameters_sanitizer_Alpha', 'sanitizeValue',
                ],
            ],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            ['parameterNameToBeSanitized' => '1'],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesPrefersCommonRulesOverDefaultRules()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => '"1testValue"',
        ];
        $rules = [
            'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => [
                'parameterNameToBeSanitized' => FILTER_SANITIZE_STRING,
            ],
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [
                'filter' => FILTER_CALLBACK,
                   'options' => [
                       'tx_mksanitizedparameters_sanitizer_Alpha', 'sanitizeValue',
                ],
            ],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            ['parameterNameToBeSanitized' => '&#34;1testValue&#34;'],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesUsesDefaultRulesIfNoSpecialsOrCommons()
    {
        $arrayToSanitize = [
            'parameterNameToBeSanitized' => '"1testValue"',
        ];
        $rules = [
            'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => [
                'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_STRING,
            ],
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [
                'filter' => FILTER_CALLBACK,
                   'options' => [
                       'tx_mksanitizedparameters_sanitizer_Alpha', 'sanitizeValue',
                ],
            ],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            ['parameterNameToBeSanitized' => 'testValue'],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesPrefersCommonRulesOverDefaultRulesWhenParameterNameInSubArray()
    {
        $arrayToSanitize = [
            'myExt' => [
                'parameterNameToBeSanitized' => '"1testValue"',
            ],
        ];
        $rules = [
            'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => [
                'parameterNameToBeSanitized' => FILTER_SANITIZE_STRING,
            ],
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => [
                'filter' => FILTER_CALLBACK,
                   'options' => [
                       'tx_mksanitizedparameters_sanitizer_Alpha', 'sanitizeValue',
                ],
            ],
        ];
        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            ['myExt' => ['parameterNameToBeSanitized' => '&#34;1testValue&#34;']],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesUsesCommonRulesInSubArrayEvenIfCommonRulesInMainArray()
    {
        $arrayToSanitize = [
            'myExt' => [
                'parameterNameToBeSanitized' => '"1testValue"',
            ],
        ];
        $rules = [
            'myExt' => [
                tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => [
                    'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                ],
            ],
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => [
                'parameterNameToBeSanitized' => FILTER_SANITIZE_STRING,
            ],
        ];

        /* @var $mainClass \tx_mksanitizedparameters*/
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

//        echo '<h1>DEBUG: ' . __FILE__ . ' Line: ' . __LINE__ . '</h1><pre>' . var_export(array(
//                        '$arrayToSanitize' => $arrayToSanitize,
//                        '$rules' => $rules,
//                        '$sanitizedArray' => $sanitizedArray,
//
//                ), true) . '</pre>';
//        exit('DEBUG: ' . __FILE__ . ' Line: ' . __LINE__);

        $this->assertEquals(
            ['myExt' => ['parameterNameToBeSanitized' => '1']],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesUsesDefaultRulesInSubArrayEvenIfDefaultRulesInMainArray()
    {
        $arrayToSanitize = [
            'myExt' => [
                'parameterNameToBeSanitized' => '"1testValue"',
            ],
        ];
        $rules = [
            'myExt' => [
                tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT,
            ],
            tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_STRING,
        ];

        /* @var $mainClass \tx_mksanitizedparameters*/
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            ['myExt' => ['parameterNameToBeSanitized' => '1']],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesMergesAndOverwritesCommonConfigIntoSubsequentLevels()
    {
        $arrayToSanitize = [
            'myExt' => [
                'parameterNameToBeSanitized' => '"1testValue"',
                'anotherParameterNameToBeSanitized' => '"1testValue"',
            ],
        ];
        $rules = [
            'myExt' => [
                tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => [
                    'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                ],
            ],
            tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => [
                'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_STRING,
            ],
        ];

        /* @var $mainClass \tx_mksanitizedparameters*/
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            [
                'myExt' => [
                    'parameterNameToBeSanitized' => '1',
                    'anotherParameterNameToBeSanitized' => '&#34;1testValue&#34;',
                ],
            ],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * @group unit
     */
    public function testSanitizeArrayByRulesWithRulesForSubArrayButSubArrayParameterItSelfIsGivenCastsFilterArrayConfigToIntegerResultingInEmptiedValue()
    {
        $arrayToSanitize = [
            'myExt' => 'test',
        ];
        $rules = [
            'myExt' => [
                'mySubParameter' => [
                    'filter' => FILTER_CALLBACK,
                    'options' => [
                        'doesNotMatter', 'doesNotMatterToo',
                    ],
                ],
            ],
        ];

        /* @var $mainClass tx_mksanitizedparameters */
        $mainClass = \DMK\MkSanitizedParameters\Factory::makeInstance('tx_mksanitizedparameters');
        $sanitizedArray = $mainClass->sanitizeArrayByRules(
            $arrayToSanitize,
            $rules
        );

        $this->assertEquals(
            ['myExt' => ''],
            $sanitizedArray,
            'The array wasn\'t sanitized correct!'
        );
    }

    /**
     * Wrapper for deprecated getMock method.
     *
     * Taken From nimut/testing-framework
     *
     * @param string $originalClassName
     * @param array  $methods
     * @param array  $arguments
     * @param string $mockClassName
     * @param bool   $callOriginalConstructor
     * @param bool   $callOriginalClone
     * @param bool   $callAutoload
     * @param bool   $cloneArguments
     * @param bool   $callOriginalMethods
     * @param null   $proxyTarget
     *
     * @throws \PHPUnit_Framework_Exception
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMock(
        $originalClassName,
        $methods = [],
        array $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true,
        $cloneArguments = false,
        $callOriginalMethods = false,
        $proxyTarget = null
    ) {
        if (method_exists($this, 'createMock')) {
            $mockBuilder = $this->getMockBuilder($originalClassName)
                ->setMethods($methods)
                ->setConstructorArgs($arguments)
                ->setMockClassName($mockClassName)
                ->setProxyTarget($proxyTarget);
            if (!$callOriginalConstructor) {
                $mockBuilder->disableOriginalConstructor();
            }
            if (!$callOriginalClone) {
                $mockBuilder->disableOriginalClone();
            }
            if (!$callAutoload) {
                $mockBuilder->disableAutoload();
            }
            if ($cloneArguments) {
                $mockBuilder->enableArgumentCloning();
            }
            if ($callOriginalMethods) {
                $mockBuilder->enableProxyingToOriginalMethods();
            }

            return $mockBuilder->getMock();
        }

        return parent::getMock(
            $originalClassName,
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload,
            $cloneArguments,
            $callOriginalMethods,
            $proxyTarget
        );
    }
}
