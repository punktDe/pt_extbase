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
class WgetTest extends \\PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
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
    public function setUp()
    {
        $this->workingDirectory = Files::concatenatePaths(array(__DIR__, 'WorkingDirectory'));
        Files::createDirectoryRecursively($this->workingDirectory);

        $this->wgetCommand = $this->objectManager->get('PunktDe\PtExtbase\Utility\Wget\WgetCommand');
        $this->wgetLogParser = $this->objectManager->get('PunktDe\PtExtbase\Utility\Wget\WgetLogParser');
    }



    /**
     * @return void
     */
    public function tearDown()
    {
        Files::removeDirectoryRecursively($this->workingDirectory);
    }


    /**
     * @test
     */
    public function downloadNotExistingPageAndDetectErrors()
    {
        $this->wgetCommand->setOutputFile(Files::concatenatePaths(array($this->workingDirectory, 'wget.log')))
            ->setDirectoryPrefix($this->workingDirectory)
            ->setNoVerbose(true)
            ->setServerResponse(true)
            ->setUrl('http://localhost/not-existing-file.html')
            ->execute();

        $log = $this->wgetLogParser->parseLog($this->wgetCommand);

        $this->assertTrue($log->hasErrors());
        $this->assertCount(1, $log);

        $logEntry = $log->getItemByIndex(0); /** @var \PunktDe\PtExtbase\Utility\Wget\WgetLogEntry $logEntry */

        $this->assertEquals(404, $logEntry->getStatus());
        $this->assertEquals('http://localhost/not-existing-file.html', $logEntry->getUrl());
    }

    /**
     * @test
     */
    public function downloadExistingPage()
    {
        $this->wgetCommand->setOutputFile(Files::concatenatePaths(array($this->workingDirectory, 'wget.log')))
            ->setDirectoryPrefix($this->workingDirectory)
            ->setNoVerbose(true)
            ->setServerResponse(true)
            ->setUrl('http://localhost/')
            ->execute();

        $log = $this->wgetLogParser->parseLog($this->wgetCommand);

        $this->assertFalse($log->hasErrors());
        $this->assertFileExists(Files::concatenatePaths(array($this->workingDirectory, 'index.html')));
    }
}
