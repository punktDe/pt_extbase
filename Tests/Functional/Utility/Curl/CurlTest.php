<?php
namespace PunktDe\PtExtbase\Tests\Functional\Utility\Curl;

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
use Neos\Utility\Files;
use PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase;
use PunktDe\PtExtbase\Utility\Curl\Request;
use PunktDe\PtExtbase\Utility\Curl\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Curl Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Functional\Utility\Curl
 */
class CurlTest extends AbstractBaseTestcase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Curl\Request
     */
    protected $curlRequest;

    public function setUp(): void
    {
        $this->curlRequest = GeneralUtility::makeInstance(Request::class);

        $headers = $this->generateHeaders();

        foreach ($headers as $header => $value) {
            $this->curlRequest->addHeader($header, $value);
        }

    }

    public function tearDown(): void
    {
    }

    /**
     * @return string[]
     */
    protected function generateHeaders(): array
    {
        return [
            'cache-control' => 'no-cache',
            'authorization' => 'testtest',
            'content-type' => 'application/json'
        ];
    }


    /**
     * @test
     */
    public function successfulPostRequestGeneratesResponse()
    {
        $response = $this->curlRequest->setUrl('http://example.com/')->post();

        $this->assertInstanceOf(Response::class, $response);

        $this->assertTrue($response->isRequestSucceeded());
        $this->assertEquals('200', $response->getHttpCode());
        $this->assertEquals(0, $response->getErrorNumber());

        $this->assertFalse(stristr($response->getBody(), 'HTTP/1.1 200 OK'));

        $this->assertTrue(is_array($response->getHeader()));
        $this->assertEquals('HTTP/1.1 200 OK', $response->getHeader('http_code'));
        $this->assertContains('text/html', $response->getHeader('Content-Type'));
    }


    /**
     * @test
     */
    public function postRequestToNotExistingDomain()
    {
        $response = $this->curlRequest->setUrl('http://nonExistent.url')->post();

        $this->assertInstanceOf(Response::class, $response);

        $this->assertFalse($response->isRequestSucceeded());
        $this->assertEquals(0, $response->getHttpCode());
        $this->assertEquals(6, $response->getErrorNumber());
    }
}
