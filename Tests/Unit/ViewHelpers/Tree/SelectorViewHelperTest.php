<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <knoll@punkt.de>
*  All rights reserved
*
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
 * Testcase for Tree SelectorViewHelper
 *
 * @package pt_extbase
 * @subpackage Tests\ViewHelpers\Tree
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Tests_Unit_ViewHelpers_Tree_SelectorViewhelperTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var \PunktDe\PtExtbase\ViewHelpers\Tree\SelectorViewHelper
     */
    protected $accessibleProxyClass;

    /**
     * @var \PunktDe\PtExtbase\ViewHelpers\Javascript\TemplateViewHelper
     */
    protected $accessibleProxy;


    public function setUp(): void
    {
        $this->accessibleProxyClass = $this->buildAccessibleProxy('\PunktDe\PtExtbase\ViewHelpers\Tree\SelectorViewHelper');
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
        $this->assertTrue(class_exists('\PunktDe\PtExtbase\ViewHelpers\Tree\SelectorViewHelper'));
    }
}
