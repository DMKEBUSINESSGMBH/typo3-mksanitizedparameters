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
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * Configuration utility.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ConfigurationUtility implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var array<string, string>
     */
    protected $extensionConfiguration = null;

    /**
     * The extension configuration!
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return int|string|mixed|null
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getExtensionConfiguration($key, $default = null)
    {
        if (null === $this->extensionConfiguration) {
            $this->extensionConfiguration = [];

            if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mksanitizedparameters'])) {
                $this->extensionConfiguration = unserialize(
                    $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mksanitizedparameters']
                );
            }

            if (Typo3Utility::isTypo3Version9OrHigher()) {
                $this->extensionConfiguration = Factory::makeInstance(ExtensionConfiguration::class)->get(
                    'mksanitizedparameters',
                    ''
                );
            }
        }

        if (empty($this->extensionConfiguration[$key])) {
            return $default;
        }

        return $this->extensionConfiguration[$key];
    }

    /**
     * Is the debug mode enabled?
     *
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return (bool) $this->getExtensionConfiguration('debugMode');
    }

    /**
     * Is the log mode enabled?
     *
     * @return bool
     */
    public function isLogMode(): bool
    {
        return (bool) $this->getExtensionConfiguration('logMode');
    }

    /**
     * Is the stealth mode enabled?
     *
     * @return bool
     */
    public function isStealthMode(): bool
    {
        return (bool) $this->getExtensionConfiguration('stealthMode');
    }

    /**
     * Is the stealth mode enabled?
     *
     * @return int
     */
    public function getStealthModeStoragePid(): int
    {
        return (int) $this->getExtensionConfiguration('stealthModeStoragePid');
    }
}
