<?php
namespace PunktDe\PtExtbase\Tests\Unit\Utility;

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
 * Real URL Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Unit\Utility
 */
class RealUrlTest extends UnitTestCase {

	/**
	 * @var \PunktDe\PtExtbase\Utility\RealUrl
	 */
	protected $proxy;



	/**
	 * @return void
	 */
	public function setUp() {
		$proxyClass = $this->buildAccessibleProxy('PunktDe\PtExtbase\Utility\RealUrl');
		$this->proxy = new $proxyClass();
	}



	/**
	 * @return array
	 */
	public function filterPathFromUrlReturnsValidPathDataProvider() {
 		return array(
		    'urlWithSchemeAndDomain' => array(
			    'url' => 'http://www.kubrick.co.uk/a/clockwork/orange.html',
			    'expected' => 'a/clockwork/orange'
		    ),
		    'urlWithoutSchemeAndWithDomain' => array(
			    'url' => 'www.kubrick.co.uk/a/clockwork/orange.html',
			    'expected' => 'a/clockwork/orange'
		    ),
		    'urlWithoutSchemeWithDomainWithHtmSuffix' => array(
			    'url' => 'www.kubrick.co.uk/a/clockwork/orange.htm',
			    'expected' => 'a/clockwork/orange'
		    ),
	    );
	}



	/**
	 * @test
	 * @dataProvider filterPathFromUrlReturnsValidPathDataProvider
	 *
	 * @param string $url
	 * @param string $expected
	 */
	public function filterPathFromUrlReturnsValidPath($url, $expected) {
		$actual = $this->proxy->filterPathFromUrl($url);
		$this->assertSame($expected, $actual);
	}

}
