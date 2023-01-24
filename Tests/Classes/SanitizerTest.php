<?php

declare(strict_types=1);

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mksanitizedparameters" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

namespace DMK\MkSanitizedParameters;

use DMK\MkSanitizedParameters\Input\ArrayInput;
use DMK\MkSanitizedParameters\Sanitizer\AlphaSanitizer;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SanitizerTest extends AbstractTestCase
{
    /**
     * @test
     *
     * @group unit
     *
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
    public static function getSanitizeInputData()
    {
        return [
            // sanitize array by rules returns untouched array if rules empty
            __LINE__.':SanitizeArrayByRulesReturnsUntouchedArrayIfRulesEmpty' => [
                '$inputData' => [
                    'get' => '4a',
                    'post' => '7b',
                ],
                '$rules' => [
                    Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT,
                ],
                '$sanitizedData' => [
                    'get' => '4',
                    'post' => '7',
                ],
            ],
            // sanitize array by rules returns untouched array if rules empty
            __LINE__.':SanitizeArrayByRulesReturnsUntouchedArrayIfRulesEmpty' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '1testValue',
                ],
                '$rules' => [],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => '1testValue',
                ],
            ],
            // sanitize array by rules returns untouched array without rules for given parameter
            __LINE__.':SanitizeArrayByRulesReturnsUntouchedArrayWithoutRulesForGivenParameter' => [
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
            __LINE__.':SanitizeArrayByRulesWorksCorrectWithUnconfiguredValuesButDefaultRules' => [
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
            __LINE__.':SanitizeArrayByRulesWorksCorrectWithFlatArrayAndSingleFilterConfig' => [
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
            __LINE__.':SanitizeArrayByRulesWorksCorrectWithFlatArrayAndFilterConfigAsArray' => [
                '$inputData' => [
                    'parameterInRange' => '<span>me&you</span>',
                    'parameterOutOfRange' => '<span>me&you</span>',
                ],
                '$rules' => [
                    'parameterInRange' => [
                        'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                    ],
                    'parameterOutOfRange' => [
                        'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                        'flags' => FILTER_FLAG_ENCODE_AMP,
                    ],
                ],
                '$sanitizedData' => [
                    'parameterInRange' => '&lt;span&gt;me&amp;you&lt;/span&gt;',
                    'parameterOutOfRange' => '&lt;span&gt;me&amp;you&lt;/span&gt;',
                ],
            ],
            // sanitize array by rules works correct with unconfigured values and no default rules
            __LINE__.':SanitizeArrayByRulesWorksCorrectWithUnconfiguredValuesAndNoDefaultRules' => [
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
            __LINE__.':SanitizeArrayByRulesWorksCorrectWithMultiDimensionalArray' => [
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
                                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
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
                            'parameterNameToBeSanitized' => '&lt;span&gt;me&amp;you&lt;/span&gt;',
                        ],
                    ],
                    'parameterNameToBeSanitizedByDefault' => 'libgd%3Cscript%3E',
                ],
            ],
            // sanitize array by rules works correct with multi dimensional array and default rules only for sub array
            __LINE__.':SanitizeArrayByRulesWorksCorrectWithMultiDimensionalArrayAndDefaultRulesOnlyForSubArray' => [
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
            __LINE__.':SanitizeArrayByRulesWorksCorrectWithSeveralConfiguredFiltersAsFilterArray' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '<span>Is your name O\'reilly & are sure about that?</span>',
                ],
                '$rules' => [
                    'parameterNameToBeSanitized' => [
                        'filter' => [
                            FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                            FILTER_SANITIZE_ADD_SLASHES,
                        ],
                        'flags' => FILTER_FLAG_ENCODE_AMP,
                    ],
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => '&lt;span&gt;Is your name O&#039;reilly &amp; are sure about that?&lt;/span&gt;',
                ],
            ],
            // sanitize array by rules works correct with several configured filters as list
            __LINE__.':SanitizeArrayByRulesWorksCorrectWithSeveralConfiguredFiltersAsList' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '<span>Is your name O\'reilly & are sure about that?</span>',
                ],
                '$rules' => [
                    'parameterNameToBeSanitized' => [
                        FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                        FILTER_SANITIZE_ADD_SLASHES,
                    ],
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => '&lt;span&gt;Is your name O&#039;reilly &amp; are sure about that?&lt;/span&gt;',
                ],
            ],
            // sanitize array by rules works correct with custom filter
            __LINE__.':SanitizeArrayByRulesWorksCorrectWithCustomFilter' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => 'abc123',
                ],
                '$rules' => [
                    'parameterNameToBeSanitized' => [
                        'filter' => FILTER_CALLBACK,
                        'options' => [
                            AlphaSanitizer::class, 'sanitizeValue',
                        ],
                    ],
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => 'abc',
                ],
            ],
            // sanitize array by rules prefers special rules over common rules
            __LINE__.':SanitizeArrayByRulesPrefersSpecialRulesOverCommonRules' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '"1testValue"',
                ],
                '$rules' => [
                    'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                    Rules::COMMON_RULES_KEY => [
                        'parameterNameToBeSanitized' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                    ],
                    Rules::DEFAULT_RULES_KEY => [
                        'filter' => FILTER_CALLBACK,
                        'options' => [
                            AlphaSanitizer::class, 'sanitizeValue',
                        ],
                    ],
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => '1',
                ],
            ],
            // sanitize array by rules prefers common rules over default rules
            __LINE__.':SanitizeArrayByRulesPrefersCommonRulesOverDefaultRules' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '"1testValue"',
                ],
                '$rules' => [
                    'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                    Rules::COMMON_RULES_KEY => [
                        'parameterNameToBeSanitized' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                    ],
                    Rules::DEFAULT_RULES_KEY => [
                        'filter' => FILTER_CALLBACK,
                        'options' => [
                            AlphaSanitizer::class, 'sanitizeValue',
                        ],
                    ],
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => '&quot;1testValue&quot;',
                ],
            ],
            // sanitize array by rules uses default rules if no specials or commons
            __LINE__.':SanitizeArrayByRulesUsesDefaultRulesIfNoSpecialsOrCommons' => [
                '$inputData' => [
                    'parameterNameToBeSanitized' => '"1testValue"',
                ],
                '$rules' => [
                    'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                    Rules::COMMON_RULES_KEY => [
                        'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                    ],
                    Rules::DEFAULT_RULES_KEY => [
                        'filter' => FILTER_CALLBACK,
                        'options' => [
                            AlphaSanitizer::class, 'sanitizeValue',
                        ],
                    ],
                ],
                '$sanitizedData' => [
                    'parameterNameToBeSanitized' => 'testValue',
                ],
            ],
            // sanitize array by rules prefers common rules over default rules when parameter name in sub array
            __LINE__.':SanitizeArrayByRulesPrefersCommonRulesOverDefaultRulesWhenParameterNameInSubArray' => [
                '$inputData' => [
                    'myExt' => ['parameterNameToBeSanitized' => '"1testValue"'],
                ],
                '$rules' => [
                    'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                    Rules::COMMON_RULES_KEY => [
                        'parameterNameToBeSanitized' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                    ],
                    Rules::DEFAULT_RULES_KEY => [
                        'filter' => FILTER_CALLBACK,
                        'options' => [
                            AlphaSanitizer::class, 'sanitizeValue',
                        ],
                    ],
                ],
                '$sanitizedData' => [
                    'myExt' => ['parameterNameToBeSanitized' => '&quot;1testValue&quot;'],
                ],
            ],
            // Sanitize Array By Rules Uses Common Rules In Sub Array Even If Common Rules In Main Array
            __LINE__.':SanitizeArrayByRulesUsesCommonRulesInSubArrayEvenIfCommonRulesInMainArray' => [
                '$inputData' => [
                    'myExt' => ['parameterNameToBeSanitized' => '"1testValue"'],
                ],
                '$rules' => [
                    'myExt' => [
                        Rules::COMMON_RULES_KEY => [
                            'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                        ],
                    ],
                    Rules::COMMON_RULES_KEY => [
                        'parameterNameToBeSanitized' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                    ],
                ],
                '$sanitizedData' => [
                    'myExt' => ['parameterNameToBeSanitized' => '1'],
                ],
            ],
            // sanitize array by rules uses default rules in sub array even if default rules in main array
            __LINE__.':SanitizeArrayByRulesUsesDefaultRulesInSubArrayEvenIfDefaultRulesInMainArray' => [
                '$inputData' => [
                    'myExt' => ['parameterNameToBeSanitized' => '"1testValue"'],
                ],
                '$rules' => [
                    'myExt' => [
                        Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT,
                    ],
                    Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                ],
                '$sanitizedData' => [
                    'myExt' => ['parameterNameToBeSanitized' => '1'],
                ],
            ],
            // sanitize array by rules merges and overwrites common config into subsequent levels
            __LINE__.':SanitizeArrayByRulesMergesAndOverwritesCommonConfigIntoSubsequentLevels' => [
                '$inputData' => [
                    'myExt' => [
                        'parameterNameToBeSanitized' => '"1testValue"',
                        'anotherParameterNameToBeSanitized' => '"1testValue"',
                    ],
                ],
                '$rules' => [
                    'myExt' => [
                        Rules::COMMON_RULES_KEY => [
                            'parameterNameToBeSanitized' => FILTER_SANITIZE_NUMBER_INT,
                        ],
                    ],
                    Rules::COMMON_RULES_KEY => [
                        'anotherParameterNameToBeSanitized' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                    ],
                ],
                '$sanitizedData' => [
                    'myExt' => [
                        'parameterNameToBeSanitized' => '1',
                        'anotherParameterNameToBeSanitized' => '&quot;1testValue&quot;',
                    ],
                ],
            ],
            // sanitize array by rules with rules for sub array but sub array parameter
            // it self is given casts filter array config to integer resulting in emptied value
            __LINE__.':SanitizeArrayByRulesWithRulesForSubArrayButSubArrayParameterItSelfIsGivenCastsFilterArrayConfigToIntegerResultingInEmptiedValue' => [
                '$inputData' => [
                    'myExt' => 'test',
                ],
                '$rules' => [
                    'myExt' => [
                        'mySubParameter' => [
                            'filter' => FILTER_CALLBACK,
                            'options' => [
                                'doesNotMatter', 'doesNotMatterToo',
                            ],
                        ],
                    ],
                ],
                '$sanitizedData' => [
                    'myExt' => '',
                ],
            ],
        ];
    }
}
