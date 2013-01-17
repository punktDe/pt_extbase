<?php

class Tx_PtExtbase_Tests_Unit_Assertion_AssertTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/**
	 * Holds temporary instance of dbObj in Assert class.
	 *
	 * @var object
	 */
	private $dbObjTmp;



	/** @test */
	public function isAThrowsNoExceptionIfAssertionHolds() {
		// This test should pass without an Exception being thrown
		Tx_PtExtbase_Assertions_Assert::isA($this, 'Tx_PtExtbase_Tests_Unit_Assertion_AssertTest', array('message' => 'Message that shows if assertion does not hold.'));
	}



	/** @test */
	public function isAThrowsExceptionIfAssertionDoesNotHold() {
		// This test should throw an exception
		try {
			Tx_PtExtbase_Assertions_Assert::isA($this, 'Tx_PtExtbase_Tests_Unit_Assertion_AssertTestFakeInterface', array('message' => 'This throws an exception'));
		} catch (Exception $e) {
			return;
		}
		$this->fail('We should get an exception, as we tested whether a class implements an interface which the class does not implement.');
	}



	/** @test */
	public function classExistsThrowsNoExceptionIfAssertionHolds() {
		// This test should pass without an Exception being thrown
		Tx_PtExtbase_Assertions_Assert::classExists('Tx_PtExtbase_Tests_Unit_Assertion_AssertTest', array('message' => 'Message that shows if assertion does not hold'));
	}



	/** @test */
	public function classExistsThrowsAnExceptionIfAssertionDoesNotHold() {
		try {
			Tx_PtExtbase_Assertions_Assert::classExists('Tx_PtExtbase_Tests_Unit_Assertion_AssertTestNONEXISTING', array('message' => 'This throws an exception'));
		} catch(Exception $e) {
			return;
		}
		$this->fail('We should get an exception, as we wanted to assert the existence of a non-existent class.');
	}



	/** @test */
	public function tableExistsThrowsNoExceptionIfTableExists() {
		$dbObjMock = $this->getMock('t3lib_DB',array('admin_get_tables'), array(), '', FALSE);
		$dbObjMock->expects($this->any())->method('admin_get_tables')->will($this->returnValue(array('pages' => array())));
		$this->saveDbObjInAssertClass();
		Tx_PtExtbase_Assertions_Assert::$dbObj = $dbObjMock;
		Tx_PtExtbase_Assertions_Assert::tableExists('pages');
		$this->restoreDbObjInAssertClass();
	}



	/** @test */
	public function tableExistsThrowsExceptionIfTableDoesNotExist() {
		$dbObjMock = $this->getMock('t3lib_DB', array('admin_get_tables'), array(), '', FALSE);
		$dbObjMock->expects($this->any())->method('admin_get_tables')->will($this->returnValue(array()));
		$this->saveDbObjInAssertClass();
		Tx_PtExtbase_Assertions_Assert::$dbObj = $dbObjMock;
		try {
			Tx_PtExtbase_Assertions_Assert::tableExists('pages');
		} catch(Exception $e) {
			$this->restoreDbObjInAssertClass();
			// Test is passed, since we get an Exception
			return;
		}
		$this->restoreDbObjInAssertClass();
		// Test fails since we did not get an exception
		$this->fail('Expected exception for asserting that a non-existing table exists was not thrown!');
	}



	/** @test */
	public function tableAndFieldExistThrowsNoExceptionIfFieldExistsInTable() {
		$dbMockObj = $this->getMock('t3lib_DB', array('admin_get_fields'), array(), '', FALSE);
		$dbMockObj->expects($this->any())->method('admin_get_fields')->with('table')->will($this->returnValue(array('fieldname' => array())));
		$this->saveDbObjInAssertClass();
		Tx_PtExtbase_Assertions_Assert::$dbObj = $dbMockObj;
		Tx_PtExtbase_Assertions_Assert::tableAndFieldExist('table', 'fieldname');
		$this->restoreDbObjInAssertClass();
	}



	private function saveDbObjInAssertClass() {
		$this->dbObjTmp = Tx_PtExtbase_Assertions_Assert::$dbObj;
	}



	private function restoreDbObjInAssertClass() {
		Tx_PtExtbase_Assertions_Assert::$dbObj = $this->dbObjTmp;
	}

}



class Tx_PtExtbase_Tests_Unit_Assertion_AssertTestFakeInterface {}