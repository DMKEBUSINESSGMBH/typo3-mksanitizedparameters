<?php

declare(strict_types=1);

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

namespace DMK\MkSanitizedParameters\Domain\Repository;

use DMK\MkSanitizedParameters\Factory;
use DMK\MkSanitizedParameters\Input\InputInterface;
use Doctrine\DBAL\Driver\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * Monitor entry repository.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class MonitorRepository
{
    public function getTableName(): string
    {
        return 'tx_mksanitizedparameters';
    }

    protected function getConnection(): Connection
    {
        return Factory::makeInstance(ConnectionPool::class)->getConnectionForTable($this->getTableName());
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getConnection()->createQueryBuilder();
    }

    public function createSearchQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()->select('*')->from($this->getTableName());
    }

    public function countByInput(InputInterface $input): int
    {
        $queryBuilder = $this->createQueryBuilder();
        $result = $queryBuilder->count('uid')
                ->from('tx_mksanitizedparameters')
                ->where(
                    $queryBuilder->expr()->eq(
                        'hash',
                        $queryBuilder->createNamedParameter(
                            $this->createHashForInput($input)
                        )
                    )
                )
                ->executeQuery();

        if (!$result instanceof Result) {
            return 0;
        }

        return $result->fetchOne();
    }

    public function insertInput(InputInterface $input): ?string
    {
        $inputRecord = [
            'name' => $input->getName(),
            'crdate' => time(),
            'pid' => Factory::getConfiguration()->getStealthModeStoragePid(),
            'hash' => $this->createHashForInput($input),
            'value' => $this->getValuesOutput($input->getInputArray()),
        ];

        $connection = $this->getConnection();
        $connection->createQueryBuilder()->insert($this->getTableName())->values($inputRecord)->executeStatement();

        return $connection->lastInsertId();
    }

    private function createHashForInput(InputInterface $input): string
    {
        return md5($input->getName().$this->getValuesOutput($input->getInputArray()));
    }

    /**
     * @param array<string, mixed> $array
     */
    private function getValuesOutput(array $array): string
    {
        return var_export($array, true);
    }
}
