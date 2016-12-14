<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
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

use PunktDe\PtExtbase\Utility\FakeFrontendFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class implements a testcase for the fake frontend creation
 */
class Tx_PtExtbase_Tests_Unit_Utility_FakeFrontendFactoryTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    public function setUp()
    {
        unset($GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function classExists()
    {
        $this->assertTrue(class_exists(FakeFrontendFactory::class));
    }

    /**
     * @test
     */
    public function fakeFrontendCreatesGlobalsTsfe()
    {
        $this->assertNull($GLOBALS['TSFE']);

        /** @var $fakeFrontend FakeFrontendFactory */
        $fakeFrontend = GeneralUtility::makeInstance(FakeFrontendFactory::class);
        $fakeFrontend->createFakeFrontEnd(1);

        $this->assertInstanceOf(TypoScriptFrontendController::class, $GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function fakeFrontendContainsCObj()
    {
        $this->assertNull($GLOBALS['TSFE']);

        /** @var $fakeFrontend FakeFrontendFactory */
        $fakeFrontend = GeneralUtility::makeInstance(FakeFrontendFactory::class);
        $fakeFrontend->createFakeFrontEnd(1);

        $this->assertNotNull($GLOBALS['TSFE']->cObj, 'No Cobject in faked frontend.');
        $this->assertInstanceOf(ContentObjectRenderer::class, $GLOBALS['TSFE']->cObj);
    }
}
