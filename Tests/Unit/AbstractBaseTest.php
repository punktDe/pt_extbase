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
 * Class implements a base testcase for pt_extbase testcases
 *
 * @package Tests
 * @author Michael Knoll <knoll@punkt.de>
 */
abstract class Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * Returns a mocked Tx_Fluid_View_TemplateView object with a mocked assign method.
	 *
	 * @return Tx_Fluid_View_TemplateView The mocked view class
	 */
	public function getViewMockWithMockedAssignMethod() {
		return $this->getMock('Tx_Fluid_View_TemplateView', array('assign'), array(), '', FALSE);
	}

}
