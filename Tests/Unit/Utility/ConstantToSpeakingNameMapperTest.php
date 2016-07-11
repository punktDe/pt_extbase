<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
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
 * Test case for Tx_PtExtbase_Utility_ConstantToSPeakingNameMapper
 *
 * @package pt_extbase
 * @subpackage Tests\Unit\Utility
 * @see Tx_PtExtbase_Utility_ConstantToSpeakingNameMapper
 */
class Tx_PtExtbase_Tests_Unit_Utility_ConstantToSpeakingNameMapper extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var string
     */
    protected $proxyClass;

    /**
     * @var Tx_PtExtbase_Utility_ConstantToSpeakingNameMapper
     */
    protected $proxy;

    public function setUp()
    {
        $this->proxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Tests_Unit_Utility_ConstantToSpeakingNameMapperMock');
        $this->proxy = new $this->proxyClass();
    }

    public function tearDown()
    {
        unset($this->proxy);
    }

    /**
     * @test
     */
    public function originalSpeakingNameToConstantMappingIsValid()
    {
        $expected = array(
            'FOO_BAR' => 6
        );
        $actual = $this->proxy->_get('originalSpeakingNameToConstantMapping');
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getSpeakingNameToConstantMapReturnsValidMap()
    {
        $speakingNameToConstantMap = array(
            'FOO_BAR' => 6
        );
        $expected = $speakingNameToConstantMap;

        $this->proxy->_set('speakingNameToConstantMap', $speakingNameToConstantMap);

        $actual = $this->proxy->getSpeakingNameToConstantMap();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getConstantToSpeakingNameMapReturnsValidMap()
    {
        $constantToSpeakingNameMap = array(
            6 => 'FOO_BAR'
        );
        $expected = $constantToSpeakingNameMap;

        $this->proxy->_set('constantToSpeakingNameMap', $constantToSpeakingNameMap);

        $actual = $this->proxy->getConstantToSpeakingNameMap();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getConstantFromSpeakingNameReturnsValidConstant()
    {
        $speakingNameToConstantMap = array(
            'FOO_BAR' => 6
        );

        $this->proxy->_set('speakingNameToConstantMap', $speakingNameToConstantMap);

        $expected = 6;
        $actual = $this->proxy->getConstantFromSpeakingName('FOO_BAR');
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getSpeakingNameFromConstantReturnsValidSpeakingName()
    {
        $constantToSpeakingNameMap = array(
            6 => 'FOO_BAR'
        );

        $this->proxy->_set('constantToSpeakingNameMap', $constantToSpeakingNameMap);

        $expected = 'FOO_BAR';
        $actual = $this->proxy->getSpeakingNameFromConstant(6);
        $this->assertSame($expected, $actual);
    }
}



class Tx_PtExtbase_Tests_Unit_Utility_ConstantToSpeakingNameMapperMock extends Tx_PtExtbase_Utility_ConstantToSpeakingNameMapper
{
    protected function getClassName()
    {
        return 'Tx_PtExtbase_Tests_Unit_Utility_ConstantTestInterface';
    }

    protected function buildSpeakingNameToConstantMap()
    {
    }

    protected function buildConstantToSpeakingNameMap()
    {
    }
}



interface Tx_PtExtbase_Tests_Unit_Utility_ConstantTestInterface
{
    const FOO_BAR = 6;
}
