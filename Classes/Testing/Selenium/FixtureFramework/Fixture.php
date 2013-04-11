<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 punkt.de GmbH
 *  Authors:
 *    Joachim Mathes <mathes@punkt.de>,
 *    Sascha Dörr <doerr@punkt.de>
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
 * Fixture
 *
 * @package pt_extbase
 * @subpackage Testing\Selenium\FixtureFramework
 */
class Tx_PtExtbase_Testing_Selenium_FixtureFramework_Fixture {

	/**
	 * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	protected $connection;

	/**
	 * @var PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	protected $dataSet;

	/**
	 * @var PHPUnit_Extensions_Database_Operation_IDatabaseOperation
	 */
	protected $setUpOperation;

	public function __construct() {
		$this->setUpOperation = PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
	}

	/**
	 * Returns the test database connection.
	 *
	 * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	public function getConnection() {
		return $this->connection;
	}

	/**
	 * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
	 * @return Tx_PtExtbase_Testing_Selenium_FixtureFramework_Fixture
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
		return $this;
	}

	/**
	 * Returns the test dataset.
	 *
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet() {
		return $this->dataSet;
	}

	/**
	 * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
	 * @return Tx_PtExtbase_Testing_Selenium_FixtureFramework_Fixture
	 */
	public function setDataSet($dataSet) {
		$this->dataSet = $dataSet;
		return $this;
	}

	/**
	 * Returns the database operation executed in test setup.
	 *
	 * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
	 */
	public function getSetUpOperation() {
		return $this->setUpOperation;
	}

	/**
	 * @param PHPUnit_Extensions_Database_Operation_IDatabaseOperation $setUpOperation
	 * @return Tx_PtExtbase_Testing_Selenium_FixtureFramework_Fixture
	 */
	public function setSetUpOperation($setUpOperation) {
		$this->setUpOperation = $setUpOperation;
		return $this;
	}
}

?>