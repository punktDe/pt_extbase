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
use PunktDe\PtExtbase\Utility\TcaManager;

/**
 * TCA Manager test case
 *
 * @package pt_dppp_zca
 * @subpackage Tests\Unit\Domain\Utlity
 */
class Tx_PtExtBase_Utility_TcaManagerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    protected $proxyClass;

    protected $proxy;

    protected $pagesTca;

    public function setUp()
    {
        $this->proxyClass = $this->buildAccessibleProxy(TcaManager::class);
        $this->proxy = new $this->proxyClass();
        $this->pagesTca = $GLOBALS['TCA']['pages'];
    }

    public function tearDown()
    {
        $GLOBALS['TCA']['pages'] = $this->pagesTca;
        unset($this->proxy);
    }

    /**
     * @test
     */
    public function deactivateAndActivateDeleteFlag()
    {
        $proxyMock = $this->getMockBuilder($this->proxyClass)->setMethods(null)->getMock();

        $this->assertEquals("deleted", $proxyMock->deactivateDeletedFlag('pages'));
        $this->assertEquals("", $GLOBALS['TCA']['pages']['ctrl']['delete']);

        $proxyMock->activateDeletedFlag('pages', 'deleted');

        $this->assertEquals("deleted", $GLOBALS['TCA']['pages']['ctrl']['delete']);
    }


    /**
     * @test
     */
    public function deactivateAndSetEnableColumns()
    {
        $enableColumnsArray = ['test1'=>'TEST1','test2'=>'TEST2','test3'=>'TEST3'];
        $mergedEnableColumns = $GLOBALS['TCA']['pages']['ctrl']['enablecolumns'] = array_merge($GLOBALS['TCA']['pages']['ctrl']['enablecolumns'], $enableColumnsArray);
        $toDeactivate = ['test1','test2'];

        $proxyMock = $this->getMockBuilder($this->proxyClass)->setMethods(null)->getMock();

        $this->assertEquals(['test1'=>'TEST1', 'test2'=>'TEST2'], $reEnableColumns = $proxyMock->deactivateEnableColumns('pages', $toDeactivate));
        $this->assertEquals('TEST3', $GLOBALS['TCA']['pages']['ctrl']['enablecolumns']['test3']);

        $proxyMock->setEnableColumns('pages', $reEnableColumns);

        $this->assertEquals($mergedEnableColumns, $GLOBALS['TCA']['pages']['ctrl']['enablecolumns']);
    }
}
