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

use DMK\MkSanitizedParameters\AbstractTestCase;
use DMK\MkSanitizedParameters\Input\ArrayInput;
use Doctrine\DBAL\Result;
use PHPUnit\Framework\Attributes\DataProvider;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class MonitorRepositoryTest extends AbstractTestCase
{
    #[DataProvider('countResults')]
    public function testCountReturnsAnInteger(int|string|false $value, int $expected): void
    {
        $result = $this->createMock(Result::class);
        $result->method('fetchOne')->willReturn($value);
        $builder = $this->createMock(QueryBuilder::class);
        $builder->method('count')->willReturnSelf();
        $builder->method('from')->willReturnSelf();
        $builder->method('where')->willReturnSelf();
        $builder->expects($this->once())->method('createNamedParameter')
            ->with(md5('post'.var_export(['password' => '[REDACTED]'], true)))
            ->willReturn(':hash');
        $expression = $this->createMock(ExpressionBuilder::class);
        $expression->method('eq')->willReturn('hash = :hash');
        $builder->method('expr')->willReturn($expression);
        $builder->method('executeQuery')->willReturn($result);
        $this->registerConnection($builder);

        $this->assertSame($expected, (new MonitorRepository())->countByInput(new ArrayInput('post', ['password' => 'secret'])));
    }

    public static function countResults(): array
    {
        return [
            'integer count' => [2, 2],
            'string count' => ['2', 2],
            'zero count' => ['0', 0],
            'no row' => [false, 0],
        ];
    }

    #[DataProvider('sensitiveKeys')]
    public function testInsertRedactsNestedSecretsAndHashesRedactedValues(string $key): void
    {
        $this->setExtConf(['stealthModeStoragePid' => '14']);
        $raw = [$key => 'scalar-secret', 'form' => [$key => ['secret-one', 'secret-two'], 'title' => 'visible']];
        $redacted = [$key => '[REDACTED]', 'form' => [$key => '[REDACTED]', 'title' => 'visible']];
        $input = new ArrayInput('post', $raw);
        $builder = $this->createMock(QueryBuilder::class);
        $builder->method('insert')->willReturnSelf();
        $builder->expects($this->once())->method('values')->with($this->callback(function (array $record) use ($redacted): bool {
            $this->assertSame(var_export($redacted, true), $record['value']);
            $this->assertSame(md5('post'.var_export($redacted, true)), $record['hash']);
            $this->assertSame('post', $record['name']);
            $this->assertSame(14, $record['pid']);

            return true;
        }))->willReturnSelf();
        $builder->expects($this->once())->method('executeStatement')->willReturn(1);
        $this->registerConnection($builder);

        $this->assertSame('42', (new MonitorRepository())->insertInput($input));
        $this->assertSame($raw, $input->getInputArray());
    }

    public static function sensitiveKeys(): array
    {
        return array_map(static fn (string $key): array => [$key], [
            'password', 'pass', 'newPassword', 'user_pass', 'passwd', 'pwd',
            'access_token', 'clientSecret', 'Authorization', 'credentials',
            'api_key', 'sessionId', 'cookie', 'credit_card', 'cardNumber',
            'cvv', 'cvc', 'otp', 'totp', 'mfaCode', 'pin',
        ]);
    }

    private function registerConnection(QueryBuilder $builder): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('createQueryBuilder')->willReturn($builder);
        $connection->method('lastInsertId')->willReturn('42');
        $pool = $this->createMock(ConnectionPool::class);
        $pool->method('getConnectionForTable')->with('tx_mksanitizedparameters')->willReturn($connection);
        GeneralUtility::addInstance(ConnectionPool::class, $pool);
    }
}
