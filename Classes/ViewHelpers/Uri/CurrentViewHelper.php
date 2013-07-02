<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Daniel Lienert <daniel@lienert.cc>,
 *
 *
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
 * ViewHelper used to render a HEAD meta tag
 *
 * @author Daniel Lienert
 * @package Viewhelpers
 * @subpackage Uri
 */
class Tx_PtExtbase_ViewHelpers_Uri_CurrentViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Disable the escaping interceptor because otherwise the child nodes would be escaped before this view helper
	 * can decode the text's entities.
	 *
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * @param bool $absolute
	 * @param array $additionalParams
	 * @return string
	 */
	public function render($absolute = TRUE, $additionalParams = array()) {

		if($absolute === TRUE) {
			$uri = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
		} else {
			$uri = t3lib_div::getIndpEnv('REQUEST_URI');
		}

		return $uri;
	}
}


?>
