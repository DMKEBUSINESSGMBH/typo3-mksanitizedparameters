<?php

declare(strict_types=1);

namespace DMK\MkSanitizedParameters;

/***************************************************************
 * Copyright notice
 *
 * (c) 2020 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use DMK\MkSanitizedParameters\Input\ArrayInput;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SanitizerTest extends AbstractTestCase
{
    /**
     * @test
     * @group unit
     * @dataProvider getSanitizeInputData
     */
    public function sanitizeInputSanitizesCorrectByRules(
        array $inputData,
        array $rules,
        array $sanitizedData
    ) {
        $this->addRules($rules);

        $input = Factory::createInput(ArrayInput::class, 'TestInput', $inputData);
        Factory::getSanitizer()->sanitizeInput($input);

        $this->assertSame($sanitizedData, $input->getInputArray());
    }

    /**
     * Returns the required data for sanitizeInput test.
     *
     * @return array[]
     */
    public function getSanitizeInputData()
    {
        return [
            // sanitize array by rules returns untouched array if rules empty
            'SanitizeArrayByRulesReturnsUntouchedArrayIfRulesEmpty' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '1testValue',
                ],
                '$rules' => [],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => '1testValue',
                ],
            ],
            // sanitize array by rules returns untouched array without rules for given parameter
            'SanitizeArrayByRulesReturnsUntouchedArrayWithoutRulesForGivenParameter' => [
                '$inputData' => [
                    'parameterNameWithoutRules' => '1testValue',
                ],
                '$rules' => [
                    'unexistentParameter' => FILTER_SANITIZE_NUMBER_INT,
                ],
                '$sanitizedData' => [
                    'parameterNameWithoutRules' => '1testValue',
                ],
            ],
            // sanitize array by rules works correct with unconfigured values but default rules
            'SanitizeArrayByRulesWorksCorrectWithUnconfiguredValuesButDefaultRules' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '1testValue',
                ],
                '$rules' => [
                    Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT,
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => '1',
                ],
            ],
            // sanitize array by rules works correct with flat array and single filter config
            'SanitizeArrayByRulesWorksCorrectWithFlatArrayAndSingleFilterConfig' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '1testValue',
                ],
                '$rules' => [
                    'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => '1',
                ],
            ],
            // sanitize array by rules works correct with flat array and filter config as array
            'SanitizeArrayByRulesWorksCorrectWithFlatArrayAndFilterConfigAsArray' => [
                '$inputData' => [
                    'parameterInRange' => '<span>me&you</span>',
                    'parameterOutOfRange' => '<span>me&you</span>',
                ],
                '$rules' => [
                    'parameterInRange' => [
                        'filter' => FILTER_SANITIZE_STRING,
                    ],
                    'parameterOutOfRange' => [
                        'filter' => FILTER_SANITIZE_STRING,
                        'flags' => FILTER_FLAG_ENCODE_AMP,
                    ],
                ],
                '$sanitizedData' => [
                    'parameterInRange' => 'me&you',
                    'parameterOutOfRange' => 'me&#38;you',
                ],
            ],
            // sanitize array by rules works correct with unconfigured values and no default rules
            'SanitizeArrayByRulesWorksCorrectWithUnconfiguredValuesAndNoDefaultRules' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '1testValue',
                    'parameterNameNotToBeSanitized' => '1testValue',
                ],
                '$rules' => [
                    'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => '1',
                    'parameterNameNotToBeSanitized' => '1testValue',
                ],
            ],
            // sanitize array by rules works correct with multi dimensional array
            'SanitizeArrayByRulesWorksCorrectWithMultiDimensionalArray' => [
                '$inputData' => [
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
                ],
                '$rules' => [
                    Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_ENCODED,
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
                ],
                '$sanitizedData' => [
                    'firstExtensionQualifier' => [
                        'parameterNameToBeSanitized' => '1',
                        'parameterNameToBeSanitizedByDefault' => 'libgd%3Cscript%3E',
                    ],
                    'secondExtensionQualifier' => [
                        'subArray' => [
                            'parameterNameToBeSanitized' => 'me&#38;you',
                        ],
                    ],
                    'parameterNameToBeSanitizedByDefault' => 'libgd%3Cscript%3E',
                ],
            ],
            // sanitize array by rules works correct with multi dimensional array and default rules only for sub array
            'SanitizeArrayByRulesWorksCorrectWithMultiDimensionalArrayAndDefaultRulesOnlyForSubArray' => [
                '$inputData' => [
                    'firstExtensionQualifier' => [
                        'parameterNameToBeSanitizedByDefault' => '1testValue',
                    ],
                    'parameterNameToBeSanitizedByDefault' => 'libgd<script>',
                ],
                '$rules' => [
                    Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_ENCODED,
                    'firstExtensionQualifier' => [
                        Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT,
                    ],
                ],
                '$sanitizedData' => [
                    'firstExtensionQualifier' => [
                        'parameterNameToBeSanitizedByDefault' => '1',
                    ],
                    'parameterNameToBeSanitizedByDefault' => 'libgd%3Cscript%3E',
                ],
            ],
            // sanitize array by rules works correct with several configured filters as filter array
            'SanitizeArrayByRulesWorksCorrectWithSeveralConfiguredFiltersAsFilterArray' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '<span>Is your name O\'reilly & are sure about that?</span>',
                ],
                '$rules' => [
                    'parameterNameToBeSanitized' => [
                        'filter' => [
                            FILTER_SANITIZE_STRING,
                            FILTER_SANITIZE_MAGIC_QUOTES,
                        ],
                        'flags' => FILTER_FLAG_ENCODE_AMP,
                    ],
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => 'Is your name O&#39;reilly &#38; are sure about that?',
                ],
            ],
            // sanitize array by rules works correct with several configured filters as list
            'SanitizeArrayByRulesWorksCorrectWithSeveralConfiguredFiltersAsList' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '<span>Is your name O\'reilly & are sure about that?</span>',
                ],
                '$rules' => [
                    'parameterNameToBeSanitized' => [
                        FILTER_SANITIZE_STRING,
                        FILTER_SANITIZE_MAGIC_QUOTES,
                    ],
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => 'Is your name O&#39;reilly & are sure about that?',
                ],
            ],
            // sanitize array by rules works correct with custom filter
            'SanitizeArrayByRulesWorksCorrectWithCustomFilter' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => 'abc123',
                ],
                '$rules' => [
                    'parameterNameToBeSanitized' => [
                        'filter' => FILTER_CALLBACK,
                        'options' => [
                            'tx_mksanitizedparameters_sanitizer_Alpha', 'sanitizeValue',
                        ],
                    ],
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => 'abc',
                ],
            ],
        ];
    }
}
