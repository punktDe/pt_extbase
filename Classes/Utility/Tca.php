<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Daniel Lienert <lienert@punkt.de>, Joachim Mathes <mathes@punkt.de>
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
 * TCA Service
 *
 * @package pt_extbase
 * @subpackage Utility
 */
class Tx_PtExtbase_Utility_Tca implements t3lib_Singleton {

	/**
	 * Extension Name
	 * @var string
	 */
	protected $extensionName;


	/**
	 * @var string
	 */
	protected $table;


	 /**
     * @var Tx_Extbase_Configuration_ConfigurationManager
     */
    protected $configurationManager;



	 /**
     * @param Tx_Extbase_Configuration_ConfigurationManager $configurationManager
     * @return void
     */
    public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManager $configurationManager) {
        $this->configurationManager = $configurationManager;
    }



	/**
     * Initialize the object (called by objectManager)
     *
     */
   public function initializeObject() {
		$frameWorkKonfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$this->extensionName = $frameWorkKonfiguration['extensionName'];

		$this->includeTcaForFrontend();
	}



	/**
	 * Set the extension namespace
	 *
	 * @param $extensionName
	 * @return Tx_PtExtbase_Utility_Tca
	 */
	public function setExtensionName($extensionName) {
		$this->extensionName = $extensionName;
		return $this;
	}


	/**
	 * Set the according table
	 *
	 * @param $table
	 * @return Tx_PtExtbase_Utility_Tca
	 */
	public function setTable($table) {
		if($table !== $this->table) {
			$this->table = $table;
			t3lib_div::loadTCA($table);
		}

		return $this;
	}



	/**
	 * Get the TCA settings by namespace
	 *
	 * @param $nameSpace
	 * @param null $table
	 * @return array
	 */
	public function getSettingsByNamespace($nameSpace, $table = null) {
		if($table) $this->setTable($table);
		$settings = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($GLOBALS['TCA'][$this->table],$nameSpace);

		return $settings;
	}
	


	/**
	 * Include TCA for the frontend
	 *
	 * @return void
	 */
	protected function includeTcaForFrontend() {
		$GLOBALS['TSFE']->includeTCA();
	}
}