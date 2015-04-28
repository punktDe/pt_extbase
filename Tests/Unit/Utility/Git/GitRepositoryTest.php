<?php
namespace PunktDe\PtExtbase\Tests\Unit\Utility\Git;

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

use \TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Git Repository Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Unit\Utility\Git
 */
class GitRepositoryTest extends UnitTestCase {

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
		$this->prepareProxy();
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

		$this->getMockBuilder('PunktDe\PtExtbase\Utility\ShellCommandService')
			->setMethods(array('execute'))
			->setMockClassName('ShellCommandServiceMock')
			->getMock();
		$this->shellCommandServiceMock = $objectManager->get('ShellCommandServiceMock'); /** @var  $shellCommandServiceMock \PHPUnit_Framework_MockObject_MockObject */
		$this->objectContainer->registerImplementation('PunktDe\PtExtbase\Utility\ShellCommandService', 'ShellCommandServiceMock');

		$proxyClass = $this->buildAccessibleProxy('PunktDe\PtExtbase\Utility\Git\GitRepository');

		$this->getMockBuilder($proxyClass)
			->setMethods(array('initializeObject'))
			->setMockClassName('GitRepositoryMock')
			->getMock();
		$this->proxy = $objectManager->get('GitRepositoryMock', '/usr/bin/git', '~');
	}



	/**
	 * @test
	 */
	public function commandRendersValidCommand() {
		$this->prepareShellCommandExpectations();

		$this->proxy->cloneRepository()
			->setRepository('file:///path/to/a/repository/of/chocolate.git')
			->setDirectory('/path/to/checked/out/chocolate/')
			->execute();

		$this->proxy->cloneRepository()
			->setRepository('file:///path/to/a/repository/of/chocolate.git')
			->setDirectory('/path/to/checked/out/chocolate/')
			->setDepth(1)
			->setBranch('YummyTag')
			->execute();

		$this->proxy->checkout()
			->setForce(TRUE)
			->setQuiet(TRUE)
			->setCommit('c0ca3ae2f34ef4dc024093f92547b43a4d9bd58a')
			->execute();

		$this->proxy->log()
			->setMaxCount(10)
			->execute();

		$this->proxy->log()
			->setFormat('%H')
			->execute();

		$this->proxy->config()
			->setGlobal(TRUE)
			->setUserName('Bud Spencer')
			->execute();

		$this->proxy->config()
			->setGlobal(TRUE)
			->setEmail('bud@spencer.it')
			->execute();

		$this->proxy->remote()
			->remove()
			->setName('origin')
			->execute();

		$this->proxy->remote()
			->add()
			->setName('origin')
			->setUrl('file:///tmp/punktde.git')
			->execute();

		$this->proxy->init()
			->setBare(TRUE)
			->setShared(TRUE)
			->execute();

		$this->proxy->push()
			->setTags()
			->setRemote('origin')
			->setRefspec('master')
			->execute();

		$this->proxy->tag()
			->setName('v1.2.3')
			->setSign(TRUE)
			->setAnnotate(TRUE)
			->setMessage('Release')
			->execute();

		$this->proxy->commit()
			->setAllowEmpty(TRUE)
			->setMessage('This is a very empty commit!')
			->execute();

		$this->proxy->commit()
			->setMessage('This is a very cool message!')
			->execute();

		$this->proxy->add()
			->setPath('.')
			->setAll(TRUE)
			->execute();

		$this->proxy->status()
			->setShort(TRUE)
			->execute();

		$this->proxy->log()
			->setNameOnly(TRUE)
			->execute();
	}



	/**
	 * @return void
	 */
	protected function prepareShellCommandExpectations() {
		$this->shellCommandServiceMock->expects($this->any())
			->method('execute')
			->withConsecutive(
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git clone file:///path/to/a/repository/of/chocolate.git /path/to/checked/out/chocolate/')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git clone --branch YummyTag --depth 1 file:///path/to/a/repository/of/chocolate.git /path/to/checked/out/chocolate/')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git checkout --force --quiet c0ca3ae2f34ef4dc024093f92547b43a4d9bd58a')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git log --max-count=10')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git log --pretty="%H"')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git config --global user.name "Bud Spencer"')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git config --global user.email "bud@spencer.it"')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git remote remove origin')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git remote add origin file:///tmp/punktde.git')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git init --bare --shared')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git push --tags origin master')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git tag -s -a -m "Release" v1.2.3')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git commit --allow-empty --message "This is a very empty commit!"')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git commit --message "This is a very cool message!"')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git add --all .')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git status --short --untracked-files=all')),
				array($this->equalTo('cd ~; /usr/bin/git --git-dir=~/.git log --name-only'))
			);
	}

}
