<?php

namespace PunktDe\PtExtbase\Tests\Unit\Utility\Curl;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use PunktDe\PtExtbase\Utility\Curl\Request;
use PunktDe\PtExtbase\Utility\Curl\Response;

/**
 * @package pt_extbase
 * @subpackage Tests\Unit\Domain\Utlity
 */
class ResponseTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Curl\Response
     */
    protected $curlResponse;


    public function setUp()
    {
        $curlRequest = new Request();

        $proxyClass = $this->buildAccessibleProxy(Response::class);

        $this->curlResponse = new $proxyClass(curl_init(), $curlRequest, "header\r\n\r\nbody");
    }


    public function proxyHeaderDataProvider()
    {
        return [
            'Proxy Header found' => [
                'original' => "HTTP/1.1 200 Connection established\r\n\r\nHTTP/1.1 204 No Content\r\nDate: Tue, 07 Apr 2015 09:05:44 GMT\r\nX-Powered-By: Servlet/3.0\r\nX-CSRFTOKEN: tfFJ1bMcEhLR2T1Zo2h9wKC",
                'expected' => "HTTP/1.1 204 No Content\r\nDate: Tue, 07 Apr 2015 09:05:44 GMT\r\nX-Powered-By: Servlet/3.0\r\nX-CSRFTOKEN: tfFJ1bMcEhLR2T1Zo2h9wKC"
            ],
            'No Proxy Header found' => [
                'original' => "HTTP/1.1 204 No Content\r\nDate: Tue, 07 Apr 2015 09:05:44 GMT\r\nX-Powered-By: Servlet/3.0\r\nX-CSRFTOKEN: tfFJ1bMcEhLR2T1Zo2h9wKC",
                'expected' => "HTTP/1.1 204 No Content\r\nDate: Tue, 07 Apr 2015 09:05:44 GMT\r\nX-Powered-By: Servlet/3.0\r\nX-CSRFTOKEN: tfFJ1bMcEhLR2T1Zo2h9wKC"
            ],
        ];
    }


    /**
     * @test
     * @dataProvider proxyHeaderDataProvider
     *
     * @param $original
     * @param $expected
     */
    public function stripProxyHeader($original, $expected)
    {
        $actual = $this->curlResponse->stripProxyHeader($original);
        $this->assertEquals($expected, $actual);
    }
}
