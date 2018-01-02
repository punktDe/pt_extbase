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
class Tx_PtExtbase_Tests_Unit_ViewHelpers_Be_FormTokenViewHelperTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var Tx_PtExtbase_ViewHelpers_Be_FormTokenViewHelper
     */
    protected $accessibleProxyClass;

    /**
     * @var Tx_PtExtbase_ViewHelpers_Be_FormTokenViewHelper
     */
    protected $accessibleProxy;


    public function setUp()
    {
        $this->accessibleProxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_ViewHelpers_Be_FormTokenViewHelper');
        $this->accessibleProxy = new $this->accessibleProxyClass();
    }

    public function tearDown()
    {
        unset($this->accessibleProxy);
    }

    /**
     * @test
     */
    public function classExists()
    {
        $this->markTestSkipped("won't be fixed getUrlToken will removed in TYPO3 8");
        $this->assertTrue(class_exists('Tx_PtExtbase_ViewHelpers_Be_FormTokenViewHelper'));
    }


    /**
     * @test
     */
    public function formTokenViewHelperReturnsFormTokenString()
    {
        $this->markTestSkipped("won't be fixed getUrlToken will removed in TYPO3 8");
        $formToken = $this->accessibleProxy->render();
        $secondFormToken = \TYPO3\CMS\Backend\Utility\BackendUtility::getUrlToken('tceAction');

        $this->assertEquals($formToken, $secondFormToken);
    }
}
