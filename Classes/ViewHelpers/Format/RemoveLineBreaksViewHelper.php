<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Daniel Lienert <daniel@lienert.cc>
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
 * Remove Line Breaks ViewHelper
 *
 * @package pt_extbase
 * @subpackage ViewHelpers\Format
 */
class Tx_PtExtbase_ViewHelpers_Format_RemoveLineBreaksViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('string', 'string', 'The string to remove the linebreaks', FALSE);
	}

	/**
	 *  Render
	 *
	 * @return string
	 */
	public function render() {
		$input = $this->arguments['string'];
		if ($input === NULL) {
			$input = $this->renderChildren();
		}
		if (is_string($input)) {
			$result = preg_replace("/\n\r|\r\n|\n|\r/", "", $input);
		}

		return $result;
	}
	
}
