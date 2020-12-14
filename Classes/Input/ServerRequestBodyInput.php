<?php

declare(strict_types=1);

namespace DMK\MkSanitizedParameters\Input;

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

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ServerRequestBodyInput extends ServerRequestQueryInput implements InputInterface
{
    public function getName(): string
    {
        return 'ServerRequestBody';
    }

    public function isSanitizingNecessary(): bool
    {
        $body = $this->request->getParsedBody();

        if (!is_array($body)) {
            //@TODO: sanitizing of objects not supported yet!
            return false;
        }

        return !empty($body);
    }

    public function getInputArray(): array
    {
        return $this->isSanitizingNecessary() ? $this->request->getParsedBody() : [];
    }

    public function setCleanedInputArray(array $cleaned): void
    {
        $this->request = $this->request->withParsedBody($cleaned);
    }
}
