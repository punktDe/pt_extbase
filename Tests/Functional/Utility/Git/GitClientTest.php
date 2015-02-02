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
			\Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->repositoryRootPath);
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
				\Tx_PtExtbase_Utility_Files::removeDirectoryRecursively($this->repositoryRootPath);
			}
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
		$this->proxy->_set('objectManager', $this->objectManager);
		$this->proxy->_set('shellCommandService', $this->objectManager->get('PunktDe\PtExtbase\Utility\ShellCommandService'));
	}



	/**
	 * @test
	 */
	public function logCommandRendersValidLogCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\LogCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\LogCommand $command */
		$command->setNameOnly(TRUE);
		$expected = 'cd ~; /usr/bin/git log --name-only';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function statusCommandRendersValidStatusCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\StatusCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\StatusCommand $command */
		$command->setShort(TRUE);
		$expected = 'cd ~; /usr/bin/git status --short';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function addCommandRendersValidAddCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\AddCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\AddCommand $command */
		$command->setPath('.');
		$expected = 'cd ~; /usr/bin/git add .';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function commitCommandRendersValidCommitCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\CommitCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\CommitCommand $command */
		$command->setMessage('This is a very cool message!');
		$expected = 'cd ~; /usr/bin/git commit --message "This is a very cool message!"';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function tagCommandRendersValidTagCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\TagCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\TagCommand $command */
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
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\PushCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\PushCommand $command */
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
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\InitCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\InitCommand $command */
		$command->setBare(TRUE);
		$command->setShared(TRUE);
		$expected = 'cd ~; /usr/bin/git init --bare --shared';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function remoteAddCommandRendersValidRemoteAddCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\RemoteCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\RemoteCommand $command */
		$command->add()
			->setName('origin')
			->setUrl('file:///tmp/punktde.git');
		$expected = 'cd ~; /usr/bin/git remote add origin file:///tmp/punktde.git';
		$actual = $this->proxy->_call('renderCommand', $command);
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function remoteRemoveCommandRendersValidRemoteRemoveCommand() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\RemoteCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\RemoteCommand $command */
		$command->remove()
			->setName('origin');
		$expected = 'cd ~; /usr/bin/git remote remove origin';
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
		$this->skipTestIfGitCommandForTestingDoesNotExist();
		$initCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\InitCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\InitCommand $initCommand */
		$addCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\AddCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\AddCommand $addCommand */
		$commitCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\CommitCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\CommitCommand $commitCommand */
		$logCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\LogCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\LogCommand $logCommand */

		$this->proxy->setCommandPath($this->pathToGitCommand);
		$this->proxy->setRepositoryRootPath($this->repositoryRootPath);

		$addCommand->setPath(".");
		$commitCommand->setMessage("[TASK] Initial commit");

		$this->proxy->execute($initCommand);

		file_put_contents($this->repositoryRootPath . DIRECTORY_SEPARATOR . "DoomDevice.txt", "Dr. Strangelove or How I Stopped Worrying And Love The Bomb");

		$this->proxy->execute($addCommand);
		$this->proxy->execute($commitCommand);
		$actual = $this->proxy->execute($logCommand);

		$expected = "[TASK] Initial commit";
		$this->assertContains($expected, $actual);
	}



	/**
	 * @test
	 */
	public function checkIfFluentInterfaceWorks() {
		$this->proxy->setCommandPath($this->pathToGitCommand);
		$this->proxy->setRepositoryRootPath($this->repositoryRootPath);

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
		$this->assertContains($expected, $actual);
	}



	/**
	 * @test
	 */
	public function checkShortGitStatusOutput() {
		$this->skipTestIfGitCommandForTestingDoesNotExist();

		$this->proxy->setCommandPath($this->pathToGitCommand);
		$this->proxy->setRepositoryRootPath($this->repositoryRootPath);

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

		$this->assertSame($expected, $actual);
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
