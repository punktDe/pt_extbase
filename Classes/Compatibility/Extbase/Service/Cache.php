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
 * Compatibility Class for the Cache Management Service
 *
 * @package Compatibility
 * @author Daniel Lienert
 */

class Tx_PtExtbase_Compatibility_Extbase_Service_Cache {


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
	 * @return CacheService
	 */
	protected function getCacheService() {
		// 4.6, 4.7
		if(class_exists('Tx_Extbase_Service_CacheService')) {
			return $this->objectManager->get('Tx_Extbase_Service_CacheService');
		}
	}



	/**
	 * @param $pageIdsToClear
	 */
	public function clearPageCache($pageIdsToClear) {
		if(class_exists('Tx_Extbase_Utility_Cache')) {
			Tx_Extbase_Utility_Cache::clearPageCache($pageIdsToClear);
		} else {
			$this->getCacheService()->clearPageCache($pageIdsToClear);
		}
	}
}
