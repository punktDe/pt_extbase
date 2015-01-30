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

/**
 * Git Client Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Functional\Utility\Git
 */
class GitClientTest extends \Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/**
	 * @var \PunktDe\PtExtbase\Utility\Git\GitClient
	 */
	protected $proxy;


	/**
	 * @var string
	 */
	protected $pathToGitCommand;


	/**
	 * @var boolean
	 */
	protected $gitCommandForTestingExists = FALSE;


	/**
	 * @var string
	 */
 	protected $repositoryRootPath = '';


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
		\Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->repositoryRootPath);
	}



	/**
	 * @return void
	 */
	protected function prepareGitEnvironment() {
		if (file_exists($this->repositoryRootPath)) {
			\Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->repositoryRootPath);
		}

		$this->pathToGitCommand = rtrim(`which git`);
		if ($this->pathToGitCommand) {
			$this->gitCommandForTestingExists = TRUE;

			$this->repositoryRootPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "RepositoryRootPath";
			mkdir($this->repositoryRootPath);
		}
	}



	/**
	 * @return void
	 */
	protected function prepareProxy() {
		$proxyClass = $this->buildAccessibleProxy('PunktDe\PtExtbase\Utility\Git\GitClient');
		$this->proxy = new $proxyClass();

		$loggerMock = $this->getMockBuilder('Tx_PtExtbase_Logger_Logger')
			->getMock();
		$this->proxy->_set('logger', $loggerMock);
	}



	/**
	 * @test
	 */
	public function logCommandRendersValidLogCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\LogCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\LogCommand $command */
		$command->setNameOnly(TRUE);
		$expected = 'cd ~; /usr/bin/git log --name-only';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function statusCommandRendersValidStatusCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\StatusCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\StatusCommand $command */
		$command->setShort(TRUE);
		$expected = 'cd ~; /usr/bin/git status --short';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function addCommandRendersValidAddCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\AddCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\AddCommand $command */
		$command->setPath('.');
		$expected = 'cd ~; /usr/bin/git add .';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function commitCommandRendersValidCommitCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\CommitCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\CommitCommand $command */
		$command->setMessage('This is a very cool message!');
		$expected = 'cd ~; /usr/bin/git commit --message "This is a very cool message!"';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function tagCommandRendersValidTagCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\TagCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\TagCommand $command */
		$command->setName('v1.2.3');
		$command->setSign(TRUE);
		$expected = 'cd ~; /usr/bin/git tag --sign v1.2.3';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function pushCommandRendersValidPushCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\PushCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\PushCommand $command */
		$command->setRemote('origin');
		$command->setRefspec('master');
		$expected = 'cd ~; /usr/bin/git push origin master';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function initCommandRendersValidInitCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\InitCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\InitCommand $command */
		$command->setBare(TRUE);
		$command->setShared(TRUE);
		$expected = 'cd ~; /usr/bin/git init --bare --shared';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function checkIfValidGitCommandIsAvailableThrowsNoExceptionIfGitExists() {
		$this->skipTestIfGitCommandForTestingDoesNotExist();
		$this->proxy->_set('objectManager', $this->objectManager);
		$this->proxy->_set('shellCommandService', $this->objectManager->get('PunktDe\PtExtbase\Utility\ShellCommandService'));
		$this->proxy->setCommandPath($this->pathToGitCommand);
		$this->proxy->_call('checkIfValidGitCommandIsAvailable');
	}



	/**
	 * @test
	 */
	public function gitWorkflowInitAddCommitLogReturnsValidInformation() {
		$initCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\InitCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\InitCommand $initCommand */
		$addCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\AddCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\AddCommand $addCommand */
		$commitCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\CommitCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\CommitCommand $commitCommand */
		$logCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\LogCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\LogCommand $logCommand */

		$addCommand->setPath(".");
		$commitCommand->setMessage("[TASK] Initial commit");

		$this->proxy->_set('objectManager', $this->objectManager);
		$this->proxy->_set('shellCommandService', $this->objectManager->get('PunktDe\PtExtbase\Utility\ShellCommandService'));

		$this->proxy->setCommandPath($this->pathToGitCommand);
		$this->proxy->setRepositoryRootPath($this->repositoryRootPath);

		$this->proxy->execute($initCommand);

		file_put_contents($this->repositoryRootPath . DIRECTORY_SEPARATOR . "DoomDevice.txt", "Dr. Strangelove or How I Stopped Worrying And Love The Bomb");

		$this->proxy->execute($addCommand);
		$this->proxy->execute($commitCommand);
		$actual = $this->proxy->execute($logCommand);

		$expected = "[TASK] Initial commit";

		$this->assertContains($expected, $actual);
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