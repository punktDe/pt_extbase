<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert <lienert@punkt.de>, Michael Knoll <knoll@punkt.de>
*  All rights reserved
*
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
 * Testcase for FE User Session adapter
 *
 * @package Tests
 * @subpackage State\Session\Storage
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_Tests_Unit_State_Session_Storage_FeUserSessionAdapterTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {
     
	/** @test */
	public function getInstanceReturnsSingletonInstanceOfSessionAdapter() {
		$firstInstance = Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter::getInstance();
		$secondInstance = Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter::getInstance();
		$this->assertTrue($firstInstance === $secondInstance);
	}
	
	
	
	/** @test */
	public function readThrowsExceptionIfNoFeUserSessionIsAvailable() {
		$tmp = $GLOBALS['TSFE']->fe_user;
		try {
			$feSessionAdapter = Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter::getInstance();
			$feSessionAdapter->read('test');
		} catch (Exception $e) {
			$GLOBALS['TSFE']->fe_user = $tmp;
			return;
		}
		$this->fail('Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter throws no Exception on read, if no user session is available!');
	}
	
	
	
	/** @test */
	public function readReturnsExpectedValueFromFeUserSession() {
		$feUserSessionMock = $this->getMock(tslib_feUserAuth, array(), array('getKey'), '', FALSE, FALSE);
		$feUserSessionMock->expects($this->once())
		    ->method(getKey)
		    ->with($this->equalTo('user'),$this->equalTo('keyxy'))
		    ->will($this->returnValue('valuexy'));
		$tmp = $GLOBALS['TSFE']->fe_user;
		$GLOBALS['TSFE']->fe_user = $feUserSessionMock;
		$feSessionAdapter = Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter::getInstance();
		$this->assertEquals($feSessionAdapter->read('keyxy'), 'valuexy');
		$GLOBALS['TSFE']->fe_user = $tmp;
	}
	
	
	
	/** @test */
	public function storeThrowsExceptionIfNoUserSessionIsAvailable() {
		$tmp = $GLOBALS['TSFE']->fe_user;
        try {
            $feSessionAdapter = Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter::getInstance();
            $feSessionAdapter->store('test', 'test');
        } catch (Exception $e) {
            $GLOBALS['TSFE']->fe_user = $tmp;
            return;
        }
        $this->fail('Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter throws no Exception on store, if no user session is available!');
	}
	
	
	
	/** @test */
	public function storeStoresGivenValueWithGivenKeyInFrontendUserSession() {
		$feUserSessionMock = $this->getMock(tslib_feUserAuth, array(), array('setKey', 'storeSessionData'), '', FALSE, FALSE);
        $feUserSessionMock->expects($this->once())
            ->method(setKey)
            ->with($this->equalTo('user'),$this->equalTo('keyxy'), $this->equalTo('valuexy'));
        $feUserSessionMock->expects($this->once())
            ->method(storeSessionData);
        $tmp = $GLOBALS['TSFE']->fe_user;
        $GLOBALS['TSFE']->fe_user = $feUserSessionMock;
        $feSessionAdapter = Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter::getInstance();
        $feSessionAdapter->store('keyxy', 'valuexy');
        $GLOBALS['TSFE']->fe_user = $tmp;
	}
	
	
	
	/** @test */
	public function deleteThrowsExceptionIfNoUserSessionIsAvailable() {
		$tmp = $GLOBALS['TSFE']->fe_user;
        try {
            $feSessionAdapter = Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter::getInstance();
            $feSessionAdapter->delete('test');
        } catch (Exception $e) {
            $GLOBALS['TSFE']->fe_user = $tmp;
            return;
        }
        $this->fail('Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter throws no Exception on store, if no user session is available!');
	}
	
	
	
	/** @test */
	public function deleteRemovesKeyFromFrontendUserSession() {
		$this->markTestIncomplete('No idea how to test this...');
	}
	
}

?>