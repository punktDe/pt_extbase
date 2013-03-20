<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
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
 * Extension Settings
 *
 * @package pt_extbase
 * @subpackage Classes\Utility
 */
class Tx_PtExtbase_Utility_ExtensionSettings implements t3lib_Singleton {

	/**
	 * @var array
	 */
	protected $extensionSettings = array();

	/**
	 * @param string $extensionKey
	 * @return array
	 */
	public function getExtensionSettings($extensionKey) {
		$this->cacheExtensionSettings($extensionKey);
		return $this->extensionSettings[$extensionKey];
	}

	/**
	 * @param string $extensionKey
	 * @param string $key
	 * @return string
	 * @throws Exception
	 *
	 * TODO: this method returns a value not a key ....
	 */
	public function getKeyFromExtensionSettings($extensionKey, $key) {
		$settings = $this->getExtensionSettings($extensionKey);
		if (!isset($settings[$key])) {
			throw new Exception('No key ' . $key . ' set in extension ' . $extensionKey . '! 1334406600');
		}
		return $settings[$key];
	}

	/**
	 * @param string $extensionKey
	 * @return array
	 */
	protected function cacheExtensionSettings($extensionKey) {
		if (!array_key_exists($extensionKey, $this->extensionSettings) && !is_array($this->extensionSettings[$extensionKey])) {
			$this->extensionSettings[$extensionKey] = array();
			$this->extensionSettings[$extensionKey] = $this->loadExtensionSettings($extensionKey);
		}
	}

	/**
	 * @param string $extensionKey
	 * @return array
	 */
	protected function loadExtensionSettings($extensionKey) {
		$settings = Tx_PtExtbase_Div::returnExtConfArray($extensionKey);
		return $settings;
	}

}

?>
