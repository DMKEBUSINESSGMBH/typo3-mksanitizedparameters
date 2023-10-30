<?php

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

use DMK\MkSanitizedParameters\Factory;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Debug Utility class.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class DebugUtility implements SingletonInterface
{
    /**
     * @var array<int, array<int, mixed>>
     */
    protected $debugStack = [];

    /**
     * Store a Debug in local stack.
     *
     * We can't echo the debug directly,
     * mksanitizedparameters runs bevor typo3 has send any headers.
     *
     * @param array<string, mixed> $data
     * @param string               $header
     */
    public function debug(array $data, string $header = 'MkSanitizedParameters Debug'): void
    {
        $this->debugStack[] = [$data, $header];
    }

    /**
     * Directly echos out debug information as HTML (or plain in CLI context).
     *
     * @param mixed  $data
     * @param string $header
     */
    public function echoDebug($data, string $header = 'MkSanitizedParameters Debug'): void
    {
        \TYPO3\CMS\Core\Utility\DebugUtility::debug($data, $header);
    }

    /**
     * Echos out debug information as HTML (or plain in CLI context)
     * after class destruction (after typo3 is ready and php shuts down).
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __destruct()
    {
        $debugStack = $this->debugStack;
        $this->debugStack = [];

        foreach ($debugStack as $stackEntry) {
            $this->echoDebug(...$stackEntry);
        }

        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()
        ) {
            $GLOBALS['TYPO3_CONF_VARS']['FE']['compressionLevel'] = 0;
            ob_flush();
        }
    }

    /**
     * @return bool
     */
    public static function isDebugMode(): bool
    {
        return Factory::getConfiguration()->isDebugMode() && DebugUtility::isDevelopmentIp();
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function isDevelopmentIp(): bool
    {
        $remoteAddr = GeneralUtility::getIndpEnv('REMOTE_ADDR');
        $devIpMask = $GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'];

        return $remoteAddr === $devIpMask || GeneralUtility::cmpIP(
            $remoteAddr,
            $devIpMask
        );
    }
}
