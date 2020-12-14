<?php

declare(strict_types=1);

namespace DMK\MkSanitizedParameters\Hook;

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

/**
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class Typo3RequestsHook
{
    /**
     * sanitize $_REQUEST, $_POST, $_GET before
     * Frontend/Backend Actions start.
     */
    public function sanitizeGlobalInputArrays(): void
    {
        $inputs = [
            Factory::createInput(GlobalGetRequestInput::class),
            Factory::createInput(GlobalPostRequestInput::class),
        ];

        if (Factory::getMonitor()->isEnabled()) {
            Factory::getMonitor()->monitorInput(...$inputs);

            return;
        }

        Factory::getSanitizer()->sanitizeInput(...$inputs);
    }
}
