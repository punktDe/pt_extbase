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
 * Utility for namespace related static functions
 *
 * @package Utility
 * @author Daniel Lienert 
 * @author Christoph Ehscheidt 
 * @author Michael Knoll 
 */
class Tx_PtExtbase_Utility_NameSpace {
	
	/**
	 * Returns part of an array according to given namespace
	 *
	 * @param array $array
	 * @param string $namespace
	 * @return array
	 */
	public static function getArrayContentByArrayAndNamespace($returnArray, $namespace) {
		
		if(!$namespace) return $returnArray;
		if(!is_array($returnArray)) $returnArray = array();
		
		$namespaceArray = self::getNamespaceArrayByNamespaceString($namespace);
		
		foreach($namespaceArray as $namespaceChunk) {
			if (array_key_exists($namespaceChunk, $returnArray)) {
			    $returnArray = $returnArray[$namespaceChunk];
			} else {
				return array();
			}
		}
		
		return $returnArray;
	}
	
	
	
	/**
	 * Converts a namespace string into a array of namespace chunks
	 *
	 * @param string $namespaceString
	 * @return array
	 */
	protected static function getNamespaceArrayByNamespaceString($namespaceString) {
		return t3lib_div::trimExplode('.', $namespaceString);
	}
	
	
	
	/**
	 * Save a value on an array position identfied by namespace
	 * 
	 * @param string $nameSpace (Namespace identifier - dot separated)
	 * @param array $array array to save the data
	 * @param mixed $data
	 * @return array
	 */
	public static function saveDataInNamespaceTree($nameSpace, array $array, $data) {
		
		$nameSpaceChunks =  t3lib_div::trimExplode('.', $nameSpace);		
		
		$key = array_pop($nameSpaceChunks);
		$pointer = &$array;
		
		foreach($nameSpaceChunks as $chunk) {		
			$pointer = &$pointer[$chunk];
		}

		$pointer[$key] = $data;
		return array_filter($array);
	}
	
}
?>