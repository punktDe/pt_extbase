<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
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
 * Class implements a factory for GET/POST Var Adapter.
 *
 * @package State
 * @subpackage GpVars
 */
class Tx_PtExtbase_State_GpVars_GpVarsAdapterFactory {
	
	/**
	 * Singleton instances of GET/POST Var Adapters.
	 * There is one gpVarsAdapter for each extensionNamespace
	 *
	 * @var array<Tx_PtExtbase_State_GpVars_GpVarsAdapter>
	 */
	private static $instances = array();
	
	
	
	/**
	 * Factory method for GET/POST Var Adapter.
	 * 
	 * @param string $extensionNameSpace 
	 * @return Tx_PtExtbase_State_GpVars_GpVarsAdapter Singleton instance of GET/POST Var Adapter.
	 */
	public static function getInstance($extensionNameSpace) {
		if (!array_key_exists($extensionNameSpace, self::$instances) || self::$instances[$extensionNameSpace] == NULL) {
			self::$instances[$extensionNameSpace] = new Tx_PtExtbase_State_GpVars_GpVarsAdapter($extensionNameSpace);
			self::$instances[$extensionNameSpace]->injectGetVars(self::extractExtensionVariables($_GET, $extensionNameSpace));
			self::$instances[$extensionNameSpace]->injectPostVars(self::extractExtensionVariables($_POST, $extensionNameSpace));
			self::$instances[$extensionNameSpace]->injectFilesVars(self::extractExtensionVariables($_FILES, $extensionNameSpace));
		}
	
		return self::$instances[$extensionNameSpace];
	}
	
		
	
	/**
	 * Remove the extension name from the variables
	 * 
	 * @param string $vars
	 * @param string $nameSpace
	 */
	protected function extractExtensionVariables($vars, $extensionNameSpace) {
		$extractedVars = $vars[$extensionNameSpace];
		if(!is_array($extractedVars)) {
			$extractedVars = array();
		}
		
		return $extractedVars;
	}
	
}

?>