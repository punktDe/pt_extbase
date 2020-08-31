<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
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
 * Unit test for abstract action controller
 * 
 * @author Michael Knoll 
 * @package Tests
 * @subpackage Controllers
 */
class Tx_PtExtbase_Tests_Unit_Controller_AbstractActionControllerTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{

    /**
     * Setting up the fixture for the tests.
     * This will be called before each single test
     */
    protected function setUp(): void
    {
        if (!defined('TYPO3_MODE')) {
            define('TYPO3_MODE', 'FE');
        }

        $GLOBALS['TSFE'] = $this->getMockBuilder(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();

    }

    /** @test */
    public function constructorReturnsControllerInstance()
    {
        $lifeCycleManagerMock = $this->getMockBuilder('\PunktDe\PtExtbase\Lifecycle\Manager')
            ->getMock(); /* @var $lifeCycleManagerMock \PunktDe\PtExtbase\Lifecycle\Manager */
        $ptExtbaseAbstractActionController = new Tx_PtExtbase_Tests_Unit_Controller_AbstractActionControllerTest_ControllerMock($lifeCycleManagerMock);
        $this->assertTrue(is_a($ptExtbaseAbstractActionController, 'Tx_PtExtbase_Controller_AbstractActionController'));
    }
    
    
    /** @test */
    public function constructedControllerHoldsLifecycleManager()
    {
        $lifeCycleManagerMock = $this->getMockBuilder('\PunktDe\PtExtbase\Lifecycle\Manager')
            ->getMock(); /* @var $lifeCycleManagerMock \PunktDe\PtExtbase\Lifecycle\Manager */
        $ptExtbaseAbstractActionController = new Tx_PtExtbase_Tests_Unit_Controller_AbstractActionControllerTest_ControllerMock($lifeCycleManagerMock);
        $this->assertTrue(is_a($ptExtbaseAbstractActionController->getLM(), '\PunktDe\PtExtbase\Lifecycle\Manager'));
    }
}

// Private class for testing abstract action controller
class Tx_PtExtbase_Tests_Unit_Controller_AbstractActionControllerTest_ControllerMock extends \PunktDe\PtExtbase\Controller\AbstractActionController
{
    public function getLM()
    {
        return $this->lifecycleManager;
    }
}
