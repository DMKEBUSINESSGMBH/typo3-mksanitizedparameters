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

namespace DMK\MkSanitizedParameters\Utility;

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

use DMK\MkSanitizedParameters\AbstractTestCase;
use ReflectionObject;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class DebugUtilityTest extends AbstractTestCase
{
    /**
     * @test
     * @group unit
     */
    public function debugDoesNotEchoButDesctructDoes()
    {
        $debugs = [
            [
                ['data' => 'test'],
                'Debug',
                'MkSanitizedParameters',
            ],
            [
                ['data' => 'another test'],
                'Debugging',
                'MkSanitizedParametersTestCase',
            ],
        ];

        $echoDebugCall = -1;
        $debugger = $this->getMockBuilder(DebugUtility::class)->setMethods(['echoDebug'])->getMock();
        $debugger
            ->expects($this->exactly(2))
            ->method('echoDebug')
            ->with(
                ...[
                    $this->callback(
                        function ($data) use ($debugs, &$echoDebugCall) {
                            $echoDebugCall = $echoDebugCall + 1;
                            $this->assertSame(
                                $debugs[$echoDebugCall][0],
                                $data,
                                'Debug data of call '.($echoDebugCall + 1).' are wrong.'
                            );

                            return true;
                        }
                    ),
                    $this->callback(
                        function ($header) use ($debugs, &$echoDebugCall) {
                            $this->assertSame(
                                $debugs[$echoDebugCall][1],
                                $header,
                                'Header of call '.($echoDebugCall + 1).' are wrong.'
                            );

                            return true;
                        }
                    ),
                    $this->callback(
                        function ($group) use ($debugs, &$echoDebugCall) {
                            $this->assertSame(
                                $debugs[$echoDebugCall][2],
                                $group,
                                'Group of call '.($echoDebugCall + 1).' are wrong.'
                            );

                            return true;
                        }
                    ),
                ]
            );

        $debuggerReflection = new ReflectionObject($debugger);
        $debugStackReflection = $debuggerReflection->getProperty('debugStack');
        $debugStackReflection->setAccessible(true);

        // there should be no stack if there was no debug call!
        $this->assertCount(
            0,
            $debugStackReflection->getValue($debugger),
            'There are already debugs. Why?'
        );

        // now we debug two times
        $debugger->debug(...$debugs[0]);
        $debugger->debug(...$debugs[1]);

        // now there should be 2 entries on stack
        $this->assertCount(
            2,
            $debugStackReflection->getValue($debugger),
            'There should be 2 debugs on stack.'
        );

        // now we simulate the php shutdown and the core debugger should be called twice
        $debugger->__destruct();

        // after shut down there should be no stack
        $this->assertCount(
            0,
            $debugStackReflection->getValue($debugger),
            'The stack should be empty after destructw as called.'
        );
    }
}
