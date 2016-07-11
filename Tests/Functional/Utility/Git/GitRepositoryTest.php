<?php
namespace PunktDe\PtExtbase\Tests\Functional\Utility\Git;

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

use PunktDe\PtExtbase\Utility\Files;

/**
 * Git Repository Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Functional\Utility\Git
 */
class GitRepositoryTest extends \\PunktDe\PtExtbase\Tests\Unit\AbstractBaseTestcase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Git\GitRepository
     */
    protected $proxy;


    /**
     * @var \PunktDe\PtExtbase\Utility\Git\GitRepository
     */
    protected $remoteProxy;


    /**
     * @var string
     */
    protected $pathToGitCommand = '';


    /**
     * @var boolean
     */
    protected $gitCommandForTestingExists = false;


    /**
     * @var string
     */
    protected $repositoryRootPath = '';


    /**
     * @var string
     */
    protected $remoteRepositoryRootPath = '';


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
    public function setUp()
    {
        $this->prepareGitEnvironment();
        $this->prepareProxy();
    }



    /**
     * @return void
     */
    public function tearDown()
    {
        if ($this->pathToGitCommand) {
            if (file_exists($this->repositoryRootPath)) {
                Files::removeDirectoryRecursively($this->repositoryRootPath);
            }
            if (file_exists($this->remoteRepositoryRootPath)) {
                Files::removeDirectoryRecursively($this->remoteRepositoryRootPath);
            }
        }
    }



    /**
     * @return void
     */
    protected function prepareGitEnvironment()
    {
        $this->pathToGitCommand = rtrim(`which git`);
        if ($this->pathToGitCommand) {
            $this->gitCommandForTestingExists = true;

            $this->repositoryRootPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "RepositoryRootPath";
            if (file_exists($this->repositoryRootPath)) {
                Files::removeDirectoryRecursively($this->repositoryRootPath);
            }
            mkdir($this->repositoryRootPath);

            $this->remoteRepositoryRootPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "RemoteRepositoryRootPath";
            if (file_exists($this->remoteRepositoryRootPath)) {
                Files::removeDirectoryRecursively($this->remoteRepositoryRootPath);
            }
            mkdir($this->remoteRepositoryRootPath);
        }
    }



    /**
     * @return void
     */
    protected function prepareProxy()
    {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

        $this->objectContainer = $objectManager->get('TYPO3\CMS\Extbase\Object\Container\Container'); /** @var \TYPO3\CMS\Extbase\Object\Container\Container $objectContainer */

        $this->getMockBuilder('\Tx_PtExtbase_Logger_Logger')
            ->setMockClassName('LoggerMock')
            ->getMock();
        $objectManager->get('LoggerMock'); /** @var  $loggerMock \PHPUnit_Framework_MockObject_MockObject */
        $this->objectContainer->registerImplementation('\Tx_PtExtbase_Logger_Logger', 'LoggerMock');

        $this->proxy = $objectManager->get('PunktDe\PtExtbase\Utility\Git\GitRepository', $this->pathToGitCommand, $this->repositoryRootPath);
        $this->remoteProxy = $objectManager->get('PunktDe\PtExtbase\Utility\Git\GitRepository', $this->pathToGitCommand, $this->remoteRepositoryRootPath);
    }



    /**
     * @test
     */
    public function checkIfValidGitCommandIsAvailableThrowsNoExceptionIfGitExists()
    {
        $this->skipTestIfGitCommandForTestingDoesNotExist();
        $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\GitRepository', $this->pathToGitCommand, $this->repositoryRootPath);
    }



    /**
     * @test
     */
    public function checkIfFluentInterfaceWorks()
    {
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
    public function checkShortGitStatusOutput()
    {
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
            ->setShort(true)
            ->execute();

        $this->assertSame($expected, $actual->getRawResult());
    }



    /**
     * @test
     */
    public function existsReturnsValidBooleanValueDependingOnTheRepositoryStatus()
    {
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
     * @test
     */
    public function pushingToARemoteRepositoryWorks()
    {
        $this->skipTestIfGitCommandForTestingDoesNotExist();

        $this->remoteProxy
            ->init()
            ->setBare(true)
            ->execute();

        $this->proxy
            ->init()
            ->execute();

        $this->proxy
            ->remote()
            ->add()
            ->setName('origin')
            ->setUrl(sprintf("file://%s", $this->remoteRepositoryRootPath))
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

        $this->proxy
            ->push()
            ->setRemote('origin')
            ->setRefspec('master')
            ->execute();

        $actual = $this->remoteProxy
            ->log()
            ->execute()
            ->getRawResult();

        $expected = "[TASK] Initial commit";
        $this->assertContains($expected, $actual);
    }



    /**
     * @test
     */
    public function pushingTagsToARemoteRepositoryWorks()
    {
        $this->skipTestIfGitCommandForTestingDoesNotExist();

        $expectedTagName = 'WhatATag';

        $this->remoteProxy
            ->init()
            ->setBare(true)
            ->execute();

        $this->proxy
            ->init()
            ->execute();

        $this->proxy
            ->remote()
            ->add()
            ->setName('origin')
            ->setUrl(sprintf("file://%s", $this->remoteRepositoryRootPath))
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

        $this->proxy
            ->tag()
            ->setSign(false)
            ->setName($expectedTagName)
            ->execute();

        $this->proxy
            ->push()
            ->setTags()
            ->setRemote('origin')
            ->setRefspec('master')
            ->execute();

        $actual = $this->remoteProxy
            ->log()
            ->execute()
            ->getRawResult();

        $expected = "[TASK] Initial commit";
        $this->assertContains($expected, $actual);
        $actualTagName = $this->runGitCommandForAssertion(sprintf("%s --git-dir=%s/.git/ tag", $this->pathToGitCommand, $this->remoteRepositoryRootPath));
        $this->assertSame($expectedTagName, trim($actualTagName));
    }



    /**
     * @test
     */
    public function checkoutChecksOutValidCommit()
    {
        $this->skipTestIfGitCommandForTestingDoesNotExist();

        $this->proxy
            ->init()
            ->execute();

        $this->proxy
            ->remote()
            ->add()
            ->setName('origin')
            ->setUrl(sprintf("file://%s", $this->remoteRepositoryRootPath))
            ->execute();

        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")), "Dr. Strangelove Or How I Stopped Worrying And Love The Bomb");
        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")), "2001 - A Space Odyssey");

        $this->proxy
            ->add()
            ->setPath(".")
            ->execute();

        $this->proxy
            ->commit()
            ->setMessage("[TASK] Initial commit")
            ->execute();

        $expectedCommitHash = $this->proxy
            ->log()
            ->setMaxCount(1)
            ->setFormat("%H")
            ->execute()
            ->getRawResult();

        $this->assertRegExp('/[a-z0-9]{40}/', $expectedCommitHash);

        unlink(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")));

        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")), "Lolita");
        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film03.txt")), "A Clockwork Orange");

        $this->proxy
            ->add()
            ->setPath(".")
            ->execute();

        $this->proxy
            ->commit()
            ->setMessage("[CHG] Change film texts")
            ->execute();

        $this->assertFileExists(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")));
        $this->assertFileNotExists(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")));
        $this->assertFileExists(Files::concatenatePaths(array($this->repositoryRootPath, "film03.txt")));

        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")), "Paths Of Glory");

        $this->proxy
            ->checkout()
            ->setForce(true)
            ->setQuiet(true)
            ->setCommit($expectedCommitHash)
            ->execute();

        $actualCommitHash = $this->proxy
            ->log()
            ->setMaxCount(1)
            ->setFormat("%H")
            ->execute()
            ->getRawResult();

        $this->assertRegExp('/[a-z0-9]{40}/', $actualCommitHash);
        $this->assertSame($expectedCommitHash, $actualCommitHash);

        $this->assertFileExists(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")));
        $this->assertStringEqualsFile(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")), "Dr. Strangelove Or How I Stopped Worrying And Love The Bomb");
        $this->assertFileExists(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")));
        $this->assertStringEqualsFile(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")), "2001 - A Space Odyssey");
        $this->assertFileNotExists(Files::concatenatePaths(array($this->repositoryRootPath, "film03.txt")));
    }



    /**
     * @test
     */
    public function cloneRepositoryClonesRepository()
    {
        $this->skipTestIfGitCommandForTestingDoesNotExist();

        $this->proxy
            ->init()
            ->execute();

        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")), "Dr. Strangelove Or How I Stopped Worrying And Love The Bomb");
        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")), "2001 - A Space Odyssey");

        $this->proxy
            ->add()
            ->setPath(".")
            ->execute();

        $this->proxy
            ->commit()
            ->setMessage("[TASK] Initial commit")
            ->execute();

        $expectedFirstCommitHash = $this->proxy
            ->log()
            ->setMaxCount(1)
            ->setFormat("%H")
            ->execute()
            ->getRawResult();

        unlink(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")));

        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")), "Lolita");
        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film03.txt")), "A Clockwork Orange");

        $this->proxy
            ->add()
            ->setPath(".")
            ->execute();

        $this->proxy
            ->commit()
            ->setMessage("[CHG] Change film texts")
            ->execute();

        $expectedSecondCommitHash = $this->proxy
            ->log()
            ->setMaxCount(1)
            ->setFormat("%H")
            ->execute()
            ->getRawResult();

        $this->proxy
            ->cloneRepository()
            ->setRepository('file://' . $this->repositoryRootPath)
            ->setDirectory($this->remoteRepositoryRootPath)
            ->execute();

        $expectedCommits = sprintf("%s [CHG] Change film texts\n%s [TASK] Initial commit\n", trim($expectedSecondCommitHash), trim($expectedFirstCommitHash));
        $actualCommits = $this->runGitCommandForAssertion(sprintf("%s --git-dir=%s/.git/ log --pretty=\"oneline\"", $this->pathToGitCommand, $this->remoteRepositoryRootPath));
        $this->assertSame($expectedCommits, $actualCommits);
    }



    /**
     * @test
     */
    public function cloneDedicatedCommitCreatesGitRepositoryContainingExactlyThatCommit()
    {
        $this->skipTestIfGitCommandForTestingDoesNotExist();

        $this->proxy
            ->init()
            ->execute();

        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")), "Dr. Strangelove Or How I Stopped Worrying And Love The Bomb");
        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")), "2001 - A Space Odyssey");

        $this->proxy
            ->add()
            ->setPath(".")
            ->execute();

        $this->proxy
            ->commit()
            ->setMessage("[TASK] Initial commit")
            ->execute();

        $this->proxy
            ->tag()
            ->setSign(false)
            ->setName("Version01")
            ->execute();

        $expectedFirstCommitHash = $this->proxy
            ->log()
            ->setMaxCount(1)
            ->setFormat("%H")
            ->execute()
            ->getRawResult();
        $expectedFirstCommitHash = trim($expectedFirstCommitHash);

        unlink(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")));

        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")), "Lolita");
        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film03.txt")), "A Clockwork Orange");

        $this->proxy
            ->add()
            ->setPath(".")
            ->execute();

        $this->proxy
            ->commit()
            ->setMessage("[CHG] Change film texts")
            ->execute();

        $this->proxy
            ->tag()
            ->setSign(false)
            ->setName("Version02")
            ->execute();

        $expectedSecondCommitHash = $this->proxy
            ->log()
            ->setMaxCount(1)
            ->setFormat("%H")
            ->execute()
            ->getRawResult();
        $expectedSecondCommitHash = trim($expectedSecondCommitHash);

        $this->proxy
            ->cloneRepository()
            ->setDepth(1)
            ->setBranch("Version01")
            ->setRepository('file://' . $this->repositoryRootPath)
            ->setDirectory($this->remoteRepositoryRootPath)
            ->execute();

        $expectedCommits = sprintf("%s [TASK] Initial commit\n", $expectedFirstCommitHash);
        $actualCommits = $this->runGitCommandForAssertion(sprintf("%s --git-dir=%s/.git log --pretty=\"oneline\"", $this->pathToGitCommand, $this->remoteRepositoryRootPath));
        $this->assertSame($expectedCommits, $actualCommits);

        Files::emptyDirectoryRecursively($this->remoteRepositoryRootPath);

        $this->proxy
            ->cloneRepository()
            ->setDepth(1)
            ->setBranch("Version02")
            ->setRepository('file://' . $this->repositoryRootPath)
            ->setDirectory($this->remoteRepositoryRootPath)
            ->execute();

        $expectedCommits = sprintf("%s [CHG] Change film texts\n", $expectedSecondCommitHash);
        $actualCommits = $this->runGitCommandForAssertion(sprintf("%s --git-dir=%s/.git log --pretty=\"oneline\"", $this->pathToGitCommand, $this->remoteRepositoryRootPath));
        $this->assertSame($expectedCommits, $actualCommits);
    }



    /**
     * @test
     */
    public function commitOfUnmodifiedRepositoryDoesNotReturnAnError()
    {
        $this->skipTestIfGitCommandForTestingDoesNotExist();

        $this->proxy
            ->init()
            ->execute();

        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film01.txt")), "Dr. Strangelove Or How I Stopped Worrying And Love The Bomb");
        file_put_contents(Files::concatenatePaths(array($this->repositoryRootPath, "film02.txt")), "2001 - A Space Odyssey");

        $this->proxy
            ->add()
            ->setPath(".")
            ->execute();

        $this->proxy
            ->commit()
            ->setMessage("[TASK] Initial commit")
            ->execute();

        $this->proxy
            ->add()
            ->setPath(".")
            ->execute();

        $result = $this->proxy
            ->commit()
            ->setAllowEmpty(true)
            ->setMessage("[TASK] Empty commit")
            ->execute();

        $this->assertSame(0, $result->getExitCode());
    }



    /**
     * @param string $command
     * @return string
     */
    protected function runGitCommandForAssertion($command)
    {
        $returnedOutput = '';
        $filePointer = popen($command, 'r');
        while (($line = fgets($filePointer)) !== false) {
            $returnedOutput .= $line;
        }
        pclose($filePointer);
        return $returnedOutput;
    }



    /**
     * @return void
     */
    protected function skipTestIfGitCommandForTestingDoesNotExist()
    {
        if (!$this->gitCommandForTestingExists) {
            $this->markTestSkipped("Can not run test on system without git");
        }
    }
}
