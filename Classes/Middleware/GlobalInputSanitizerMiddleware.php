<?php

declare(strict_types=1);

namespace DMK\MkSanitizedParameters\Middleware;

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

use DMK\MkSanitizedParameters\Factory;
use DMK\MkSanitizedParameters\Input\GlobalGetRequestInput;
use DMK\MkSanitizedParameters\Input\GlobalPostRequestInput;
use DMK\MkSanitizedParameters\Input\ServerRequestBodyInput;
use DMK\MkSanitizedParameters\Input\ServerRequestQueryInput;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class GlobalInputSanitizerMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $globalInputs = [
            Factory::createInput(GlobalGetRequestInput::class),
            Factory::createInput(GlobalPostRequestInput::class),
        ];

        if (Factory::getMonitor()->isEnabled()) {
            Factory::getMonitor()->monitorInput(
                ...array_merge(
                    $globalInputs,
                    [
                        Factory::createInput(ServerRequestQueryInput::class, $request),
                        Factory::createInput(ServerRequestBodyInput::class, $request),
                    ]
                )
            );

            return $handler->handle($request);
        }

        // sanitize  post and get
        Factory::getSanitizer()->sanitizeInput(...$globalInputs);

        // sanitize request
        return $handler->handle($this->getCleanedServerRequest($request));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ServerRequestInterface
     */
    protected function getCleanedServerRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        return $this->sanitizeBodyInput(
            $this->sanitizeQueryInput($request)
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ServerRequestInterface
     */
    protected function sanitizeQueryInput(ServerRequestInterface $request): ServerRequestInterface
    {
        $queryInput = Factory::createInput(
            ServerRequestQueryInput::class,
            $request
        );
        Factory::getSanitizer()->sanitizeInput($queryInput);

        return $queryInput->getServerRequest();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ServerRequestInterface
     */
    protected function sanitizeBodyInput(ServerRequestInterface $request): ServerRequestInterface
    {
        $queryInput = Factory::createInput(
            ServerRequestBodyInput::class,
            $request
        );
        Factory::getSanitizer()->sanitizeInput($queryInput);

        return $queryInput->getServerRequest();
    }
}
