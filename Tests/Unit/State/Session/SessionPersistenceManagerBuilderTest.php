<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
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

/**
 * Unit test for session persistence manager builder
 * 
 * @author Michael Knoll 
 * @package Tests
 * @subpackage State\Session
 * @see Tx_PtExtbase_State_Session_SessionPersistenceManagerBuilder
 */
class Tx_PtExtbase_Tests_Unit_State_Session_SessionPersistenceManagerBuilderTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /** @test */
    public function getInstanceReturnsSingletonInstanceOfSessionPersistenceManager()
    {
        $this->markTestSkipped('Does not work');
        $sessionAdapterMock = $this->getMockBuilder(Tx_PtExtbase_State_Session_Storage_SessionAdapter::class)
            ->getMock();
        $extbaseContext = $this->getMockBuilder(Tx_PtExtbase_Context::class)
            ->getMock();
        $objectManagerMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Object\ObjectManager::class)
            ->setMethods(['get'])
            ->getMock();
        $objectManagerMock->expects($this->once())->method('get')->will($this->returnValue(new Tx_PtExtbase_State_Session_SessionPersistenceManager($sessionAdapterMock)));
        $sessionPersistenceManagerBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_PtExtbase_State_Session_SessionPersistenceManagerBuilder', $extbaseContext, $objectManagerMock); /* @var $sessionPersistenceManagerBuilder Tx_PtExtbase_State_Session_SessionPersistenceManagerBuilder */
        $firstInstance = $sessionPersistenceManagerBuilder->getInstance();
        $secondInstance = $sessionPersistenceManagerBuilder->getInstance();
        $this->assertTrue(is_a($firstInstance, 'Tx_PtExtbase_State_Session_SessionPersistenceManager'), 'Not an instance of Tx_PtExtbase_State_Session_SessionPersistenceManager');
        $this->assertTrue($firstInstance === $secondInstance, 'No singleton instance!');
    }
}
