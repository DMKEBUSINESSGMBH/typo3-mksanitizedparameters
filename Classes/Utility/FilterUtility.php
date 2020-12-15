<?php

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
     * @param mixed $valueToSanitize
     * @param int|string|array<int|string, int|string|array>|null $filterOrFilterConfig
     *
     * @return mixed
     */
    public function sanitizeByRule($valueToSanitize, $filterOrFilterConfig)
    {
        if (!is_array($filterOrFilterConfig)) {
            return $this->filterValue($valueToSanitize, $filterOrFilterConfig);
        }

        return $this->sanitizeByConfig($valueToSanitize, $filterOrFilterConfig);
    }

    /**
     * @param mixed $valueToSanitize
     * @param array<int|string, int|string|array> $filterConfig
     *
     * @return mixed
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

        $filterConfig = $this->normalizeFilterConfig($filterConfig);

        foreach ($filters as $filter) {
            $valueToSanitize = $this->filterValue($valueToSanitize, $filter, $filterConfig);
        }

        return $valueToSanitize;
    }

    /**
     * @param mixed $valueToSanitize
     * @param int|string $filter
     * @param int|array<string, int|string|array>|null $filterConfig
     *
     * @return mixed
     */
    private function filterValue($valueToSanitize, $filter = FILTER_DEFAULT, $filterConfig = null)
    {
        // for wrong filter we clear the value
        // @see testSanitizeArrayByRulesWithRulesForSubArrayButSubArrayParameterItSelfIsGivenCastsFilterArrayConfigToIntegerResultingInEmptiedValue
        if (!$this->isValidFilter($filter)) {
            return '';
        }

        return filter_var(
            $valueToSanitize,
            $this->normalizeFilter($filter),
            $filterConfig
        );
    }

    /**
     * Is the given filter a valid id?
     * Ids normally between 257 - 1024.
     *
     * @param int|string $filter
     *
     * @return bool
     */
    protected function isValidFilter($filter): bool
    {
        return isset($this->getValidFilters()[$this->normalizeFilter($filter)]);
    }

    /**
     * @return array<int, string>
     */
    protected function getValidFilters(): array
    {
        static $filters;

        if (null === $filters) {
            $filters = filter_list();
            $filters = array_combine($filters, array_map('filter_id', $filters));
            $filters = array_flip($filters);
        }

        return $filters;
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
     * @param array<int|string, int|string|array> $config
     *
     * @return int|array<string, int|string|array>|null
     */
    protected function normalizeFilterConfig(array $config)
    {
        if (!is_array($config)) {
            return $this->normalizeFilter($config);
        }

        $normalized = [];

        if (isset($config['flags'])) {
            $normalized['flags'] = $config['flags'];
        }
        if (isset($config['options'])) {
            $normalized['options'] = $config['options'];
        }

        if (empty($normalized)) {
            return null;
        }

        return $normalized;
    }

    /**
     * @param mixed $initialValue
     * @param mixed $sanitizedValue
     *
     * @return bool
     */
    public function isValueChanged($initialValue, $sanitizedValue): bool
    {
        return $initialValue != $sanitizedValue;
    }
}
