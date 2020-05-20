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

use PunktDe\PtExtbase\Lifecycle\HookManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Unit test for lifecycle hook manager
 * 
 * @author Michael Knoll 
 * @package Tests
 * @subpackage Lifecycle
 */
class Tx_PtExtbase_Tests_Unit_Lifecycle_HookManagerTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /** @test */
    public function updateEndFiresUpdateOnSingletonLifecycleManager()
    {
        $hookManager = GeneralUtility::makeInstance(HookManager::class);
        $lifecycleManager = GeneralUtility::makeInstance(Manager::class);
        $lifecycleManager->updateState(-1000); // we set a state that makes no sense
        $fakeArray = []; // we need a variable for passing parameter by reference
        $hookManager->updateEnd($fakeArray, $fakeArray);
        $this->assertEquals($lifecycleManager->getState(), \PunktDe\PtExtbase\Lifecycle\Manager::END);
    }
}
