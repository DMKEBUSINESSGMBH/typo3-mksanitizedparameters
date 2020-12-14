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

use DMK\MkSanitizedParameters\Domain\Repository\MonitorRepository;
use DMK\MkSanitizedParameters\Input\InputInterface;
use DMK\MkSanitizedParameters\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MK sanitizedparameters Factory.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
final class Factory
{
    /**
     * @param string $className
     * @param array<int, mixed> $constructorArguments
     *
     * @return object the created instance
     */
    public static function makeInstance($className, ...$constructorArguments)
    {
        return GeneralUtility::makeInstance($className, ...$constructorArguments);
    }

    /**
     * Returns the Sanitizer instance.
     *
     * @return Sanitizer
     */
    public static function getSanitizer(): Sanitizer
    {
        return Factory::makeInstance(Sanitizer::class);
    }

    /**
     * Returns the Monitor instance.
     *
     * @return Monitor
     */
    public static function getMonitor(): Monitor
    {
        return Factory::makeInstance(Monitor::class);
    }

    /**
     * Returns a Inpui instance.
     *
     * @return InputInterface
     */
    public static function createInput(string $inputClassName, ...$arguments): InputInterface
    {
        $input = Factory::makeInstance($inputClassName, ...$arguments);

        if (!$input instanceof InputInterface) {
            $errorMessage = sprintf(
                'The input "%1$s" '.'has to implement the "%2$s" interface',
                ...[get_class($input), InputInterface::class]
            );
            throw new \InvalidArgumentException($errorMessage);
        }

        return $input;
    }

    /**
     * Returns the configuration utility.
     *
     * @return ConfigurationUtility
     */
    public static function getConfiguration(): ConfigurationUtility
    {
        return Factory::makeInstance(ConfigurationUtility::class);
    }

    /**
     * Returns the configuration utility.
     *
     * @return Logger
     */
    public static function getLogger(string $name): Logger
    {
        $logManager = Factory::makeInstance(
            LogManager::class
        );

        return $logManager->getLogger($name);
    }

    /**
     * Returns the monitor repository.
     *
     * @return MonitorRepository
     */
    public static function getMonitorRepository(): MonitorRepository
    {
        return Factory::makeInstance(MonitorRepository::class);
    }
}
