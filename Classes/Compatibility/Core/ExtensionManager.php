<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert
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
 * Compatibility Class for the Cache Management Service
 *
 * @package Compatibility
 * @author Daniel Lienert
 */

class Tx_PtExtbase_Compatibility_Core_ExtensionManager {


	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;



	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 */
	public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @return array
	 */
	public function getEnabledExtensionList() {

		if(class_exists('t3lib_extMgm') && method_exists('t3lib_extMgm', 'getEnabledExtensionList')) {
			$enabledExtensions = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getEnabledExtensionList());

		} else {
			$listUtility = $this->objectManager->get('\TYPO3\CMS\Extensionmanager\Utility\ListUtility'); /** @var $listUtility \TYPO3\CMS\Extensionmanager\Utility\ListUtility */
			$availableExtensions = $listUtility->getAvailableExtensions();
			$availableAndInstalledExtensions = $listUtility->getAvailableAndInstalledExtensions($availableExtensions);
			$enabledExtensions = array_keys($availableAndInstalledExtensions);
		}

		return $enabledExtensions;
	}

}
