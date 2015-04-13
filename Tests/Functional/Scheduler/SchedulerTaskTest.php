<?php

namespace PunktDe\PtExtbase\Tests\Functional\Scheduler;

/***************************************************************
 *  Copyright (C) 2015 punkt.de GmbH
 *  Authors: el_equipo <opiuqe_le@punkt.de>
 *
 *  This script is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


use TYPO3\Flow\Utility\Files;

/**
 * Test case for class PunktDe\PtExtbase\Scheduler\AbstractSchedulerTask
 *
 * @package pt_extbase
 */
class SchedulerTaskTest extends \Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/**
	 * TODO: remove if not needed for concatenation of Database paths
	 */

	protected $testRootPath = '';


	/**
	 * @test
	 */
	public function schedulerTask() {

		$this->markTestSkipped("Not implemented yet");

	}



	/**
	 * @return array of PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	protected function getBaseDatabaseFixture() {
		return array(
			new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(Files::concatenatePaths(array($this->testRootPath, 'Fixture', 'SchedulerTaskTest.yaml')))
		);
	}



}
