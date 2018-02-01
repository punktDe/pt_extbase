<?php
namespace PunktDe\PtExtbase\Tests\Functional\Utility\Lock;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lienert <lienert@punkt.de>
 *
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use PunktDe\PtExtbase\Utility\Lock\Lock;

/**
 * Wget Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Functional\Utility\Wget
 */
class MysqlLockTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var Lock
     */
    protected $mysqlLock;

    /**
     *
     */
    public function setUp()
    {
        $this->mysqlLock = $this->objectManager->get('PunktDe\\PtExtbase\\Utility\\Lock\\Lock', 'lockTest');
    }

    public function tearDown()
    {
        $this->mysqlLock->release();
    }

    /**
     * @test
     * @expectedException Exception
     * @expectedExceptionCode 1429016835
     */
    public function acquiringSharedLockThrowsException()
    {
        $this->mysqlLock->release();
        $this->objectManager->get('PunktDe\\PtExtbase\\Utility\\Lock\\Lock', 'lockTest', 'PunktDe\\PtExtbase\\Utility\\Lock\\MySqlLockStrategy', false);
    }

    /**
     * @test
     */
    public function constructAcquiresLock()
    {
        $outputArray = [];
        $returnValue = 0;
        exec(__DIR__ . '/MySqlLockTestSecondInstance.php lockTest testIfLockIsFree', $outputArray, $returnValue);
        $this->assertEquals(0, $returnValue);
    }

    /**
     * @test
     */
    public function afterReleaseLockIsFree()
    {
        $outputArray = [];
        $returnValue = 0;
        $released = $this->mysqlLock->release();

        $this->assertTrue($released);

        exec(__DIR__ . '/MySqlLockTestSecondInstance.php lockTest testIfLockIsFree', $outputArray, $returnValue);
        $this->assertEquals(1, $returnValue);
    }

    /**
     * @test
     */
    public function acquiringLockASecondTimeIsNotPossible()
    {
        $outputArray = [];
        $returnValue = 0;
        exec(__DIR__ . '/MySqlLockTestSecondInstance.php lockTest acquireExclusiveLock', $outputArray, $returnValue);
        $this->assertEquals(0, $returnValue);
    }


    /**
     * @test
     */
    public function freeingLockIsNotPossibleBySecondClient()
    {
        $outputArray = [];
        $returnValue = 0;
        exec(__DIR__ . '/MySqlLockTestSecondInstance.php lockTest freeLock', $outputArray, $returnValue);
        $this->assertEquals(0, $returnValue);
    }
}
