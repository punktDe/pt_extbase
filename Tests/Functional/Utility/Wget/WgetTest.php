<?php
namespace PunktDe\PtExtbase\Tests\Functional\Utility\Wget;

/***************************************************************
 *  Copyright (C)  punkt.de GmbH
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
use PunktDe\PtExtbase\Utility\Files;

/**
 * Wget Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Functional\Utility\Wget
 */
class WgetTest extends \Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {


	/**
	 * @var string
	 */
 	protected $workingDirectory = '';


	/**
	 * @var \PunktDe\PtExtbase\Utility\Wget\WgetCommand
	 */
	protected $wgetCommand;


	/**
	 * @var \PunktDe\PtExtbase\Utility\Wget\WgetLogParser
	 */
	protected $wgetLogParser;


	/**
	 * @return void
	 */
	public function setUp() {
		$this->workingDirectory = __DIR__ . 'WorkingDirectory';
		Files::createDirectoryRecursively($this->workingDirectory);

		$this->wgetCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Wget\WgetCommand');
		$this->wgetLogParser = $this->objectManager->get('PunktDe\PtExtbase\Utility\Wget\WgetLogParser');
	}



	/**
	 * @return void
	 */
	public function tearDown() {
		Files::removeDirectoryRecursively($this->workingDirectory);
	}


	/**
	 * @test
	 */
	public function downloadNotExistingPageAndDetectErrors() {
		$this->wgetCommand->setOutputFile(Files::concatenatePaths(array($this->workingDirectory, 'wget.log')))
			->setOutputFile($this->workingDirectory)
			->setDirectoryPrefix($this->workingDirectory)
			->setNoVerbose(TRUE)
			->setUrl('http://not.existing.punkt.de/index.html')
			->execute();

		$log = $this->wgetLogParser->parseLog($this->wgetCommand);
	}
}