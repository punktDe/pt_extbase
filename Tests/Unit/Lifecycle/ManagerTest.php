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
 * Unit test for lifecycle manager
 * 
 * @author Michael Knoll 
 * @package Tests
 * @subpackage Lifecycle
 */
class Tx_PtExtbase_Tests_Unit_Lifecycle_ManagerTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {
	
	/** @test */
	public function constructorInitializesUndefinedState() {
		$lifecycleManager = new Tx_PtExtbase_Lifecycle_Manager();
		$this->assertEquals($lifecycleManager->getState(), Tx_PtExtbase_Lifecycle_Manager::UNDEFINED);
	}
	
	
	
	/** @test */
	public function getStateReturnsStateSetBefore() {
		$lifecycleManager = new Tx_PtExtbase_Lifecycle_Manager();
		$lifecycleManager->updateState(300);
		$this->assertEquals($lifecycleManager->getState(), 300);
	}
	
	
	
	/** @test */
	public function observerCanBeRegistered() {
		$lifecycleManager = new Tx_PtExtbase_Lifecycle_Manager();
		$observeableObject = new Tx_PtExtbase_Tests_Unit_Lifecycle_ManagerTest_ObservableMock();
		$lifecycleManager->register($observeableObject);
	}
	
	
	
	/** @test */
	public function observerGetsUpadteWhenRegisteredWithUpdating() {
		$lifecycleManager = new Tx_PtExtbase_Lifecycle_Manager();
		$lifecycleManager->updateState(321);
		$observeableObject = new Tx_PtExtbase_Tests_Unit_Lifecycle_ManagerTest_ObservableMock();
		$lifecycleManager->registerAndUpdateStateOnRegisteredObject($observeableObject);
		$this->assertEquals($observeableObject->state, 321);
	}
	
	
	
	/** @test */
	public function lifecycleManagerDoesNotUpdateStateIfNotBiggerThanBefore() {
		$lifecycleManager = new Tx_PtExtbase_Lifecycle_Manager();
        $lifecycleManager->updateState(321);
        $this->assertEquals($lifecycleManager->getState(), 321);
        $lifecycleManager->updateState(300);
        $this->assertEquals($lifecycleManager->getState(), 321);
	}
	
	
	
	/** @test */
	public function nonStaticObserverCanBeRegisteredMultipleTimes() {
		$lifecycleManager = new Tx_PtExtbase_Lifecycle_Manager();
		$observeableObject1 = new Tx_PtExtbase_Tests_Unit_Lifecycle_ManagerTest_ObservableMock();
		$observeableObject1->state = 2;
		$observeableObject2 = new Tx_PtExtbase_Tests_Unit_Lifecycle_ManagerTest_ObservableMock();
		$observeableObject2->state = 2;
		$lifecycleManager->registerAndUpdateStateOnRegisteredObject($observeableObject1, false);
		$lifecycleManager->registerAndUpdateStateOnRegisteredObject($observeableObject2, false);
		$lifecycleManager->updateState(30);
		$this->assertEquals($observeableObject1->state, 30);
		$this->assertEquals($observeableObject2->state, 30);
	}
	
	
	
	/** @test */
	public function staticObserverCanBeRegisteredOnlyOnce() {
		$lifecycleManager = new Tx_PtExtbase_Lifecycle_Manager();
        $observeableObject1 = new Tx_PtExtbase_Tests_Unit_Lifecycle_ManagerTest_ObservableMock();
        $observeableObject1->state = 2;
        $observeableObject2 = new Tx_PtExtbase_Tests_Unit_Lifecycle_ManagerTest_ObservableMock();
        $observeableObject2->state = 2;
        $lifecycleManager->registerAndUpdateStateOnRegisteredObject($observeableObject1);
        $lifecycleManager->registerAndUpdateStateOnRegisteredObject($observeableObject2);
        $lifecycleManager->updateState(30);
        $this->assertEquals($observeableObject1->state, Tx_PtExtbase_Lifecycle_Manager::UNDEFINED);
        $this->assertEquals($observeableObject2->state, 30);
	}
	
}



/**
 * Class implements a mock for testing lifecycle manager
 */
require_once t3lib_extMgm::extPath('pt_extbase') . 'Classes/Lifecycle/EventInterface.php';
class Tx_PtExtbase_Tests_Unit_Lifecycle_ManagerTest_ObservableMock implements Tx_PtExtbase_Lifecycle_EventInterface {
	
	public $state;
	
	public function lifecycleUpdate($state) {
		$this->state = $state;
	}
	
}

?>