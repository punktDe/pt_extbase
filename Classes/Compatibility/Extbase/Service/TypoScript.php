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
 * Compatibility Class for TypoScript Conversion service
 *
 * @package Compatibility
 * @author Daniel Lienert
 */

class Tx_PtExtbase_Compatibility_Extbase_Service_TypoScript {

	/**
	 * @return \TYPO3\CMS\Extbase\Service\TypoScriptService
	 */
	protected static function getTypoScriptService() {
		return  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
	}



	/**
	 * @param array $plainArray
	 * @return array
	 */
	public static function  convertPlainArrayToTypoScriptArray(array $plainArray) {
		if (!class_exists('Tx_Extbase_Utility_TypoScript')) {
			return self::getTypoScriptService()->convertPlainArrayToTypoScriptArray($plainArray);
		} else {
			return Tx_Extbase_Utility_TypoScript::convertPlainArrayToTypoScriptArray($plainArray);
		}
	}



	/**
	 * @param array $array
	 * @return array
	 */
	public static function  convertTypoScriptArrayToPlainArray(array $array) {
		if (!class_exists('Tx_Extbase_Utility_TypoScript')) {
			return self::getTypoScriptService()->convertTypoScriptArrayToPlainArray($array);
		} else {
			return Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($array);
		}
	}
}
