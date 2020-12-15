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
use DMK\MkSanitizedParameters\Input\ArrayInput;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class MonitorTest extends AbstractTestCase
{
    /**
     * @test
     * @group unit
     */
    public function monitorInputChecksIsSanitizingNecessaryAndDoNotCallInsertInput()
    {
        $input1 = $this->prophesize(ArrayInput::class);
        $input1->isSanitizingNecessary()->shouldBeCalled()->willReturn(true);
        $input2 = $this->prophesize(ArrayInput::class);
        $input2->isSanitizingNecessary()->shouldBeCalled()->willReturn(false);

        $repo = $this->prophesize(MonitorRepository::class);
        $repo->countByInput($input1)->shouldBeCalled()->willReturn(1);
        $repo->insertInput()->shouldNotBeCalled();
        GeneralUtility::addInstance(MonitorRepository::class, $repo->reveal());

        Factory::getMonitor()->monitorInput($input1->reveal(), $input2->reveal());
    }

    /**
     * @test
     * @group unit
     */
    public function monitorInputCallsInsertInput()
    {
        $input = $this->prophesize(ArrayInput::class);
        $input->isSanitizingNecessary()->shouldBeCalled()->willReturn(true);

        $repo = $this->prophesize(MonitorRepository::class);
        $repo->countByInput($input)->shouldBeCalled()->willReturn(0);
        $repo->insertInput($input)->shouldBeCalledOnce();
        GeneralUtility::addInstance(MonitorRepository::class, $repo->reveal());

        Factory::getMonitor()->monitorInput($input->reveal());
    }
}
