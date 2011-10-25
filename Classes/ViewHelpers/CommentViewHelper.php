<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
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
 * CommentViewHelper
 * Displays nothing by default or the comment, if the variable "show" is set to true
 * Remove this viewheper if a appropriate viewhelper is implemented in fluid
 * 
 * @author Daniel Lienert 
 * @package ViewHelpers
 */
class Tx_PtExtbase_ViewHelpers_CommentViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {
	
	/**
	 * Return nothing or the comment if the variable is set to "show"
	 * TODO: Think about a global variable to display / hide all comments
	 * 
	 * @param boolean $show
	 * @return string 
	 */
	public function render($show = FALSE) {
		if ($show) {
			return $this->renderChildren();
		} else {
			return '';
		}
	}
}
?>