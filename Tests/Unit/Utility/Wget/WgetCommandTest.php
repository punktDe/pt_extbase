<?php

namespace PunktDe\PtExtbase\Tests\Utility\Wget;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 punkt.de GmbH
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
 * @package pt_extbase
 * @subpackage Tests\Unit\Domain\Utlity
 */
class WgetCommandTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Wget\WgetCommand
     */
    protected $wgetCommand;



    public function setUp(): void
    {
        $this->wgetCommand = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('PunktDe\PtExtbase\Utility\Wget\WgetCommand');
    }

    public function tearDown(): void
    {
    }


    /**
     * @test
     */
    public function buildUrl()
    {
        $expected = 'wget --no-check-certificate --convert-links --load-cookies=cookies.txt --execute robots=off --tries=30 --retry-connrefused --server-response --directory-prefix=2014-11-28-0958 --domains=test.punkt.de --page-requisites --output-file="2014-11-28-0958.log" --no-verbose "http://test.punkt.de/page/"';

        $this->wgetCommand
            ->setWgetBinaryPath('wget')
            ->setNoCheckCertificate(true)
            ->setExecute('robots=off')
            ->setConvertLinks(true)
            ->setLoadCookies('cookies.txt')
            ->setTries(30)
            ->setDomains('test.punkt.de')
            ->setRetryConnRefused(true)
            ->setServerResponse(true)
            ->setDirectoryPrefix('2014-11-28-0958')
            ->setPageRequisites(true)
            ->setOutputFile('2014-11-28-0958.log')
            ->setUrl('http://test.punkt.de/page/');

        $actual = $this->wgetCommand->getCommand();

        $this->assertEquals($expected, $actual);
    }
}
