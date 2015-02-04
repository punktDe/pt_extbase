<?php
namespace PunktDe\PtExtbase\Tests\Functional\Utility\Git;

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
 * Git Repository Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Functional\Utility\Git
 */
class GitRepositoryTest extends \Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/**
	 * @var \PunktDe\PtExtbase\Utility\Git\GitRepository
	 */
	protected $proxy;


	/**
	 * @var string
	 */
	protected $pathToGitCommand = '';


	/**
	 * @var boolean
	 */
	protected $gitCommandForTestingExists = FALSE;


	/**
	 * @var string
	 */
 	protected $repositoryRootPath = '';


	/**
	 * @var \TYPO3\CMS\Extbase\Object\Container\Container
	 */
	protected $objectContainer;


	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $shellCommandServiceMock;


	/**
	 * @return void
	 */
	public function setUp() {
		$this->prepareGitEnvironment();
		$this->prepareProxy();
	}



	/**
	 * @return void
	 */
	public function tearDown() {
		if ($this->pathToGitCommand && file_exists($this->repositoryRootPath)) {
			Files::removeDirectoryRecursively($this->repositoryRootPath);
		}
	}



	/**
	 * @return void
	 */
	protected function prepareGitEnvironment() {
		$this->pathToGitCommand = rtrim(`which git`);
		if ($this->pathToGitCommand) {
			$this->gitCommandForTestingExists = TRUE;

			$this->repositoryRootPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "RepositoryRootPath";
			if (file_exists($this->repositoryRootPath)) {
				Files::removeDirectoryRecursively($this->repositoryRootPath);
			}
			mkdir($this->repositoryRootPath);
		}
	}



	/**
	 * @return void
	 */
	protected function prepareProxy() {
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		$this->objectContainer = $objectManager->get('TYPO3\CMS\Extbase\Object\Container\Container'); /** @var \TYPO3\CMS\Extbase\Object\Container\Container $objectContainer */

		$this->getMockBuilder('\Tx_PtExtbase_Logger_Logger')
			->setMockClassName('LoggerMock')
			->getMock();
		$objectManager->get('LoggerMock'); /** @var  $loggerMock \PHPUnit_Framework_MockObject_MockObject */
		$this->objectContainer->registerImplementation('\Tx_PtExtbase_Logger_Logger', 'LoggerMock');

		$this->proxy = $objectManager->get('PunktDe\PtExtbase\Utility\Git\GitRepository', $this->pathToGitCommand, $this->repositoryRootPath);
	}



	/**
	 * @test
	 */
	public function checkIfValidGitCommandIsAvailableThrowsNoExceptionIfGitExists() {
		$this->skipTestIfGitCommandForTestingDoesNotExist();
		$this->objectManager->get('PunktDe\PtExtbase\Utility\Git\GitRepository', $this->pathToGitCommand, $this->repositoryRootPath);
	}



	/**
	 * @test
	 */
	public function checkIfFluentInterfaceWorks() {
		$this->proxy
			->init()
			->execute();

		file_put_contents($this->repositoryRootPath . DIRECTORY_SEPARATOR . "DoomDevice.txt", "Dr. Strangelove Or How I Stopped Worrying And Love The Bomb");

		$this->proxy
			->add()
			->setPath(".")
			->execute();

		$this->proxy
			->commit()
			->setMessage("[TASK] Initial commit")
			->execute();

		$actual = $this->proxy
			->log()
			->execute();

		$expected = "[TASK] Initial commit";
		$this->assertContains($expected, $actual->getRawResult());
	}



	/**
	 * @test
	 */
	public function checkShortGitStatusOutput() {
		$this->skipTestIfGitCommandForTestingDoesNotExist();

		$this->proxy
			->init()
			->execute();

		file_put_contents($this->repositoryRootPath . DIRECTORY_SEPARATOR . "film01.txt", "Dr. Strangelove Or How I Stopped Worrying And Love The Bomb");
		file_put_contents($this->repositoryRootPath . DIRECTORY_SEPARATOR . "film02.txt", "2001 - A Space Odyssey");

		$this->proxy
			->add()
			->setPath(".")
			->execute();

		$this->proxy
			->commit()
			->setMessage("[TASK] Initial commit")
			->execute();

		unlink($this->repositoryRootPath . DIRECTORY_SEPARATOR . "film02.txt");

		file_put_contents($this->repositoryRootPath . DIRECTORY_SEPARATOR . "film01.txt", "Lolita");
		file_put_contents($this->repositoryRootPath . DIRECTORY_SEPARATOR . "film03.txt", "A Clockwork Orange");

		$expected = " M film01.txt\n D film02.txt\n?? film03.txt\n";

		$actual = $this->proxy
			->status()
			->setShort(TRUE)
			->execute();

		$this->assertSame($expected, $actual->getRawResult());
	}



	/**
	 * @test
	 */
	public function existsReturnsValidBooleanValueDependingOnTheRepositoryStatus() {
		$this->skipTestIfGitCommandForTestingDoesNotExist();

 		$this->proxy->init()->execute();

		$result = $this->proxy->exists();
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);

		Files::removeDirectoryRecursively($this->repositoryRootPath);
		Files::createDirectoryRecursively($this->repositoryRootPath);

		$result = $this->proxy->exists();
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
	}



	/**
	 * @return void
	 */
	protected function skipTestIfGitCommandForTestingDoesNotExist() {
		if (!$this->gitCommandForTestingExists) {
			$this->markTestSkipped("Can not run test on system without git");
		}
    }

}
