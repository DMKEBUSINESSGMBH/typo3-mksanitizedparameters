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
class ArrayInput implements InputInterface
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var array<string, int|string|array>
     */
    private $data;

    /**
     * ArrayInput constructor.
     *
     * @param string $name
     * @param array<string, int|string|array> $data
     */
    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isSanitizingNecessary(): bool
    {
        return !empty($this->data);
    }

    /**
     * @return array<string, int|string|array>
     */
    public function getInputArray(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, int|string|array> $cleaned
     */
    public function setCleanedInputArray(array $cleaned): void
    {
        $this->data = $cleaned;
    }
}
