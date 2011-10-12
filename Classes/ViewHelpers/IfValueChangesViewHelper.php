<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
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
 * IfValueChangesViewHelper
 *
 * Acts like an if-ViewHelper
 * Renders the then part if the given value is something else as teh last time
 * Can be used to render headlines or structure a list
 *
 * @author Daniel Lienert 
 * @package ViewHelpers
 */
class Tx_PtExtbase_ViewHelpers_IfValueChangesViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * @var null
	 */
	protected static $lastValue = NULL;


	/**
	 * @param $value string
	 * @return string
	 */
	public function render($value) {

		if ($value != self::$lastValue) {
			self::$lastValue = $value;
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}
?>