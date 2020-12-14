<?php

namespace DMK\MkSanitizedParameters\Utility;

use DMK\MkSanitizedParameters\Factory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Filter class as wrapper for filter_var.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class FilterUtility
{
    /**
     * @param string|array $valueToSanitize
     * @param int|string|array $rule
     *
     * @return string|array
     */
    public function sanitizeByRule($valueToSanitize, $rule)
    {
        if (!is_array($rule)) {
            return filter_var($valueToSanitize, $this->normalizeFilter($rule));
        }

        return $this->sanitizeByConfig($valueToSanitize, $rule);
    }

    /**
     * @param string|array $valueToSanitize
     * @param array $filterConfig
     *
     * @return string|array
     */
    protected function sanitizeByConfig(
        $valueToSanitize,
        array $filterConfig
    ) {
        $filters = $filterConfig;

        if (isset($filterConfig['filter'])) {
            $filters = $filterConfig['filter'];
            unset($filterConfig['filter']);
            $filters = !is_array($filters) ? [$filters] : $filters;
        }

        foreach ($filters as $filter) {
            if (!is_scalar($filter)) {
                $filter = (int) $filter;
            }
            $valueToSanitize = filter_var($valueToSanitize, $this->normalizeFilter($filter), $filterConfig);
        }

        return $valueToSanitize;
    }

    /**
     * @param int|string $filter
     *
     * @return int
     */
    protected function normalizeFilter($filter): int
    {
        if (is_string($filter) && defined($filter)) {
            $filter = (int) constant($filter);
        }

        // @TODO: remove after dropping support for php 7.2 and add support for php 8
        // @see https://wiki.php.net/rfc/deprecations_php_7_4#filter_sanitize_magic_quotes
        if ((
            defined('FILTER_SANITIZE_MAGIC_QUOTES') &&
            constant('FILTER_SANITIZE_MAGIC_QUOTES') === $filter &&
            defined('FILTER_SANITIZE_ADD_SLASHES')
        )) {
            $filter = (int) constant('FILTER_SANITIZE_ADD_SLASHES');
        }

        return (int) $filter;
    }

    /**
     * @param mixed $initialValueToSanitize
     * @param mixed $valueToSanitize
     *
     * @return bool
     */
    public function isValueChanged($initialValue, $sanitizedValue): bool
    {
        return $initialValue != $sanitizedValue;
    }
}
