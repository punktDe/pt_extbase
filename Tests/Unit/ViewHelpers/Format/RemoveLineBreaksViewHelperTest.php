<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Daniel Lienet <daniel@lienert.cc>
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
 * StringToLower ViewHelper
 *
 * @package pt_extbase
 * @subpackage ViewHelpers\Format
 */
class Tx_PtExtbase_Tests_Unit_ViewHelpers_Format_RemoveLineBreaksViewHelperTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var \PunktDe\PtExtbase\ViewHelpers\Format\RemoveLineBreaksViewHelper
     */
    protected $accessibleProxyClass;

    /**
     * @var \PunktDe\PtExtbase\ViewHelpers\Format\RemoveLineBreaksViewHelper
     */
    protected $accessibleProxy;


    public function setUp(): void
    {
        $this->accessibleProxyClass = $this->buildAccessibleProxy('\PunktDe\PtExtbase\ViewHelpers\Format\RemoveLineBreaksViewHelper');
        $this->accessibleProxy = new $this->accessibleProxyClass();
    }

    public function tearDown(): void
    {
        unset($this->accessibleProxy);
    }

    /**
     * @test
     */
    public function classExists()
    {
        $this->assertTrue(class_exists('\PunktDe\PtExtbase\ViewHelpers\Format\RemoveLineBreaksViewHelper'));
    }


    /**
     * @return array
     */
    public function stringDataProvider()
    {
        return [
            '\n\r' => ['input' => "Hier mal ein \n\rUmbruch", 'expected' => 'Hier mal ein Umbruch'],
            '\r\n' => ['input' => "Hier mal ein \r\nUmbruch", 'expected' => 'Hier mal ein Umbruch'],
            '\n' => ['input' => "Hier mal ein \nUmbruch", 'expected' => 'Hier mal ein Umbruch'],
            '\r' => ['input' => "Hier mal ein \rUmbruch", 'expected' => 'Hier mal ein Umbruch']
        ];
    }


    /**
     * @param $input
     * @param $expected
     *
     * @test
     * @dataProvider stringDataProvider
     */
    public function renderTest($input, $expected)
    {
        $this->accessibleProxy->_set('arguments', ['string' => $input]);
        $result = $this->accessibleProxy->render();

        $this->assertEquals($expected, $result);
    }
}
