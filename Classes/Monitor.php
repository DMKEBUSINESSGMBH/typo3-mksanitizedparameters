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

use DMK\MkSanitizedParameters\Input\InputInterface;

/**
 * Stores the given arrays to the DB so it can be checked
 * which parameters have which values.
 *
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class Monitor
{
    /**
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return Factory::getConfiguration()->isStealthMode();
    }

    /**
     * @param InputInterface ...$inputs
     */
    public function monitorInput(InputInterface ...$inputs): void
    {
        foreach ($inputs as $input) {
            if (!$input->isSanitizingNecessary()) {
                return;
            }

            $this->writeInput($input);
        }
    }

    /**
     * @param InputInterface $input
     */
    protected function writeInput(InputInterface $input): void
    {
        if (!$input->isSanitizingNecessary()) {
            return;
        }

        $repo = Factory::getMonitorRepository();

        if ($repo->countByInput($input) > 0) {
            return;
        }

        $repo->insertInput($input);
    }
}
