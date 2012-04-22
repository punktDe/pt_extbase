<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Michael Knoll <knoll@punkt.de>, punkt.de GmbH
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
 * Class implements detector for TYPO3 mode.
 *
 * This class is mainly used for testing, as it can be mocked and hence
 * return arbitrary modes in a test.
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package rbac
 */
class Tx_PtExtbase_Utility_FeBeModeDetector {

	/**
	 * Returns mode, TYPO3 is currently run in.
	 *
	 * @return string
	 */
	public function getMode() {
		if (TYPO3_MODE == 'BE') {
			return 'BE';
		} else {
			return 'FE';
		}
	}



	/**
	 * Returns TRUE, if we are in BE mode
	 *
	 * @return bool
	 */
	public function inBackendMode() {
		return ($this->getMode() == 'BE');
	}



	/**
	 * Returns TRUE, if we are in FE mode
	 *
	 * @return bool
	 */
	public function inFrontendMode() {
		return ($this->getMode() == 'FE');
	}

}
?>