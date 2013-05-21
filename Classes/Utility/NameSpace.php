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
 * TODO write UNIT Tests for this stuff!
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
	 * @param array $returnArray
	 * @param string $namespace
	 * @return array
	 */
	public static function getArrayContentByArrayAndNamespace($returnArray, $namespace) {
		if(!$namespace) return $returnArray;
		if(!is_array($returnArray)) return array();
		
		$namespaceArray = self::getNamespaceArrayByNamespaceString($namespace);
		
		foreach($namespaceArray as $namespaceChunk) {
			if (is_array($returnArray) && array_key_exists($namespaceChunk, $returnArray)) {
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
	 * @param string $namespaceString (Namespace identifier - dot separated)
	 * @param array $array array to save the data in
	 * @param mixed $data
	 * @return array
	 */
	public static function saveDataInNamespaceTree($namespaceString, array $array, $data) {
		$nameSpaceChunks = self::getNamespaceArrayByNamespaceString($namespaceString);

		$key = array_pop($nameSpaceChunks);
		$pointer = &$array;

		foreach ($nameSpaceChunks as $chunk) {
			$pointer = &$pointer[$chunk];
		}

		$pointer[$key] = $data;

		//return self::arrayFilterRecursive($array);
		return $array;
	}
    
    
    
    /**
     * Remove a part from a data array described by namespace string
     * 
     * @param string $namespaceString namespace path to the key to remove
     * @param array $array data array
	  * @return array
     */
	public static function removeDataFromNamespaceTree($namespaceString, $array) {
		$nameSpaceChunks = self::getNamespaceArrayByNamespaceString($namespaceString);

		if (!is_array($nameSpaceChunks) || !is_array($array)) return;

		$key = array_pop($nameSpaceChunks);
		$pointer = &$array;

		foreach ($nameSpaceChunks as $chunk) {
			if(array_key_exists($chunk, $pointer)) {
				$pointer = &$pointer[$chunk];
			}
		}

		unset($pointer[$key]);

		return $array;
	}
	
	
	
	/**
	 * Recursively removes null-values from array
	 *
	 * @param array $input
	 * @return array
	 */
	protected static function arrayFilterRecursive($input) {
		foreach ($input as &$value) {
			if (is_array($value)) {
			    $value = self::arrayFilterRecursive($value);
			}
		}
		return array_filter($input, 'Tx_PtExtbase_Utility_NameSpace::valueIsGiven');
    }
	
	    
    
    /**
     * Returns true in case the values is present or is the integer Value 0
     * 
     * @param mixed $element
	  * @return boolean
     */
    protected static function valueIsGiven($element) {
        return (is_array($element) || (!empty($element) && $element !== 0 && $element !== ''));
    }
	 	
}

?>