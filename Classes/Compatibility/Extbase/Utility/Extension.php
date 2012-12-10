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
 * Compatibility Class for Extension Utility
 *
 * @package Compatibility
 * @author Daniel Lienert
 */

class Tx_PtExtbase_Compatibility_Extbase_Utility_Extension {

	/**
	 * @return \TYPO3\CMS\Extbase\Service\ExtensionService
	 */
	protected static function getExtensionService() {
		return  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\ExtensionService');
	}



	/**
	 * @param $extensionName
	 * @param $pluginName
	 * @return string
	 */
	public static function  getPluginNamespace($extensionName, $pluginName) {
		if (!class_exists('Tx_Extbase_Utility_Extension') || !method_exists('Tx_Extbase_Utility_Extension', 'getPluginNamespace')) {
			return self::getExtensionService()->getPluginNamespace($extensionName, $pluginName);
		} else {
			return Tx_Extbase_Utility_Extension::getPluginNamespace($extensionName, $pluginName);
		}
	}
}
