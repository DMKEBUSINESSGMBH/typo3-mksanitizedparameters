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

namespace DMK\MkSanitizedParameters\Middleware;

use DMK\MkSanitizedParameters\AbstractTestCase;
use DMK\MkSanitizedParameters\Input\GlobalGetRequestInput;
use DMK\MkSanitizedParameters\Input\GlobalPostRequestInput;
use DMK\MkSanitizedParameters\Input\ServerRequestBodyInput;
use DMK\MkSanitizedParameters\Input\ServerRequestQueryInput;
use DMK\MkSanitizedParameters\Monitor;
use DMK\MkSanitizedParameters\Sanitizer;
use DMK\MkSanitizedParameters\SanitizerTest;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class GlobalInputSanitizerMiddlewareTest extends AbstractTestCase
{
    /**
     * @test
     *
     * @group unit
     */
    public function processCallsMonitorCorrect()
    {
        self::assertNull($GLOBALS['TYPO3_REQUEST'] ?? null);

        $this->setExtConf(['stealthMode' => '1', 'stealthModeStoragePid' => '14']);

        $middleware = new GlobalInputSanitizerMiddleware();
        $response = $this->prophesize(ResponseInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('applicationType')->willReturn(1);
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request->reveal())->shouldBeCalledOnce()->willReturn($response->reveal());
        $monitor = $this->prophesize(Monitor::class);
        $monitor->isEnabled()->shouldBeCalledOnce()->willReturn(true);
        $monitor->monitorInput(
            Argument::type(GlobalGetRequestInput::class),
            Argument::type(GlobalPostRequestInput::class),
            Argument::type(ServerRequestQueryInput::class),
            Argument::type(ServerRequestBodyInput::class)
        )->shouldBeCalledOnce();
        // getMonitor is called twice!
        GeneralUtility::addInstance(Monitor::class, $monitor->reveal());
        GeneralUtility::addInstance(Monitor::class, $monitor->reveal());
        // on monitoring no sanitizing should be performed!
        $sanitizer = $this->prophesize(Sanitizer::class);
        $sanitizer->sanitizeInput()->shouldNotBeCalled();
        GeneralUtility::addInstance(Sanitizer::class, $sanitizer->reveal());

        $this->assertSame(
            $response->reveal(),
            $middleware->process($request->reveal(), $handler->reveal())
        );
    }

    /**
     * @test
     *
     * @group unit
     */
    public function processCallsSanitizerCorrect()
    {
        $this->setExtConf(['stealthMode' => '0']);

        $middleware = new GlobalInputSanitizerMiddleware();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('applicationType')->willReturn(1);
        $response = $this->prophesize(ResponseInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request->reveal())->shouldBeCalledOnce()->willReturn($response->reveal());

        $monitor = $this->prophesize(Monitor::class);
        $monitor->isEnabled()->shouldBeCalledOnce()->willReturn(false);
        $monitor->monitorInput()->shouldNotBeCalled();
        GeneralUtility::addInstance(Monitor::class, $monitor->reveal());

        $sanitizer = $this->prophesize(Sanitizer::class);
        // first call is to sanitize globals
        $sanitizer->sanitizeInput(
            Argument::type(GlobalGetRequestInput::class),
            Argument::type(GlobalPostRequestInput::class)
        )->shouldBeCalled();
        // second is to sanitize get request
        $sanitizer->sanitizeInput(
            Argument::type(ServerRequestQueryInput::class)
        )->shouldBeCalled();
        // third call is to sanitize get post
        $sanitizer->sanitizeInput(
            Argument::type(ServerRequestBodyInput::class)
        )->shouldBeCalled();
        GeneralUtility::addInstance(Sanitizer::class, $sanitizer->reveal());
        GeneralUtility::addInstance(Sanitizer::class, $sanitizer->reveal());
        GeneralUtility::addInstance(Sanitizer::class, $sanitizer->reveal());

        $this->assertSame(
            $response->reveal(),
            $middleware->process($request->reveal(), $handler->reveal())
        );
    }

    /**
     * @test
     *
     * @group unit
     *
     * @dataProvider getProcessCallsSanitizerAndSanitizesCorrectByRulesData
     */
    public function processCallsSanitizerAndSanitizesCorrectByRules(
        array $inputData,
        array $rules,
        array $sanitizedData
    ) {
        $this->setExtConf(['stealthMode' => '0']);
        $this->addRules($rules);

        $middleware = new GlobalInputSanitizerMiddleware();

        $request = new ServerRequest();
        $request = $request->withQueryParams($inputData);
        $request = $request->withParsedBody($inputData);
        $request = $request->withAttribute('applicationType', 1);

        $response = $this->prophesize(ResponseInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);
        // check if the right cleaned server request was handled
        $handler->handle(
            Argument::that(
                function (ServerRequest $cleanedRequest) use ($sanitizedData) {
                    $this->assertSame($sanitizedData, $cleanedRequest->getQueryParams());
                    $this->assertSame($sanitizedData, $cleanedRequest->getParsedBody());

                    return true;
                }
            )
        )->shouldBeCalledOnce()->willReturn($response->reveal());

        $this->assertSame(
            $response->reveal(),
            $middleware->process($request, $handler->reveal())
        );
    }

    /**
     * Returns the required data for sanitizeInput test.
     *
     * @return array[]
     */
    public static function getProcessCallsSanitizerAndSanitizesCorrectByRulesData()
    {
        return SanitizerTest::getSanitizeInputData();
    }
}
