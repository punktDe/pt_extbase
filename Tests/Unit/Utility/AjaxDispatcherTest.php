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
class AjaxDispatcherTest extends \Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase
{

    /**
     * @var \Tx_PtExtbase_Utility_AjaxDispatcher
     */
    protected $ajaxDispatcher;


    public function setUp()
    {
        $this->ajaxDispatcher = $this->objectManager->get('Tx_PtExtbase_Utility_AjaxDispatcher');
    }

    /**
     * @test
     */
    public function checkLegacyAllowedControllerActions()
    {
        $this->markTestIncomplete('Should be implemented');
    }

}
