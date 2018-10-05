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
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $this->mysqlLock = $this->objectManager->get(\PunktDe\PtExtbase\Utility\Lock\Lock::class, 'lockTest');
    }

    public function tearDown()
    {
        $this->mysqlLock->release();
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 1429016835
     */
    public function acquiringSharedLockThrowsException()
    {
        $this->mysqlLock->release();
        $this->objectManager->get(\PunktDe\PtExtbase\Utility\Lock\Lock::class, 'lockTest', \PunktDe\PtExtbase\Utility\Lock\MySqlLockStrategy::class , false);
    }

    /**
     * @test
     */
    public function constructAcquiresLock()
    {
        $outputArray = [];
        $returnValue = 0;
        exec(__DIR__ . '/MySqlLockTestSecondInstance.php ' . GeneralUtility::getApplicationContext() . ' lockTest testIfLockIsFree ', $outputArray, $returnValue);
        $this->assertEquals(0, $returnValue);
        $this->assertEquals([0], $outputArray);
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

        exec(__DIR__ . '/MySqlLockTestSecondInstance.php ' . GeneralUtility::getApplicationContext() . ' lockTest testIfLockIsFree', $outputArray, $returnValue);
        $this->assertEquals(0, $returnValue);
        $this->assertEquals([1], $outputArray);
    }

    /**
     * @test
     */
    public function acquiringLockASecondTimeIsNotPossible()
    {
        $outputArray = [];
        $returnValue = 0;
        exec(__DIR__ . '/MySqlLockTestSecondInstance.php ' . GeneralUtility::getApplicationContext() . ' lockTest acquireExclusiveLock', $outputArray, $returnValue);
        $this->assertEquals(0, $returnValue);
        $this->assertEquals([0], $outputArray);
    }


    /**
     * @test
     */
    public function freeingLockIsNotPossibleBySecondClient()
    {
        $outputArray = [];
        $returnValue = 0;
        exec(__DIR__ . '/MySqlLockTestSecondInstance.php ' . GeneralUtility::getApplicationContext() . ' lockTest freeLock', $outputArray, $returnValue);
        $this->assertEquals(0, $returnValue);
        $this->assertEquals([0], $outputArray);
    }
}
