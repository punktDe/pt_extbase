<?php
/***************************************************************
* Copyright notice
*
*   2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
* All rights reserved
*
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
* Debug Utility
*
* @package Utility
* @author Daniel Lienert
*/

class Tx_PtExtbase_Utility_Debug {
	
	/**
	 * Static debug class instance
	 * 
	 * @var Tx_PtExtbase_Utility_Debug
	 */
	protected static $debug = NULL;
	
	
	
	/**
	 * Debug Data
	 * 
	 * @var array
	 */
	protected $debugData = array();
	
	
	
	public static function debug($variable, $renderAs = 'html') {
		
		if(self::$debug === NULL) {
			self::$debug = new self();	
		}
		
		echo self::$debug->debugVariable($variable,0);
	}
	

	public function debugVariable($variable, $level) {
		
		//$this->debugData['debugTime'] = date('H:i:s');
		//$this->debugData['debugType'] = gettype($target);

		if ($level > 10) {
			return 'RECURSION ... ' . chr(10);
		}

		if (is_string($target)) {
			$output = $this->debugString($variable);
		} elseif (is_numeric($variable)) {
			$output = $this->debugNumeric($variable);
		} elseif (is_array($variable)) {
			$output = self::debugArray($variable, $level++);
		} elseif (is_object($variable)) {
			$output = self::debugObject($variable, $level++);
		} elseif (is_bool($variable)) {
			$output = $variable ? 'TRUE' : 'FALSE';
		} elseif (is_null($variable) || is_resource($variable)) {
			$output = gettype($variable);
		}
		return $output;

	}
	

	protected function debugString($variable) {
		return sprintf('\'<span class="debug-string">%s</span>\' (%s)', htmlspecialchars((strlen($variable) > 2000) ? substr($variable, 0, 2000) . '…' : $variable), strlen($variable));
	}

	protected function debugNumeric($variable) {
		return $variable;
	}

	public function debugArray($variable) {

	}

	public function debugObject($variable) {

	}

	
	protected function debugSingleValue($value) {
		
	}
	
	
	/**
	 * Render the debug data with fluid
	 * 
	 * @param unknown_type $debugData
	 */
	protected function renderDebugData($debugData) {
		$templateFile = t3lib_div::getFileAbsFileName('EXT:pt_extbase/Resources/Private/Templates/Debug/Debug.html');
		$view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');
		$view->setTemplatePathAndFilename($templateFile);
		$view->assign('debugData', $this->debugData);
		return $view->render();		
	}

	
	
	/**
	 * Retrieve internal Object data and split the values in different access classes
	 * 
	 * @param object $object
	 * @return array
	 */
	protected function processRawObjectData($object) {

		$rawInternalData = (array) $object;
		$classLen = strlen($class);
		$className = get_class($object); 
		
		foreach($rawInternalData as $property => $value) {
			if($property{0} == "\0") {
				
				if($property{1} == '*'){
					$visibility = 'protected';
					$property = substr($property, 3);
				}
				elseif(substr($property, 1, $classLen) == $className){
					$visibility = 'private';
					$property = substr($property, $classLen + 2);
				}
			}
			
			else {
				$visibility = 'public';
			}

			// TODO don't know, what you want to do here!
//			
//			switch (gettype($value)) {
//				case 
//			}
//			
			$internalData[$visibility][$property] = $value;
		}
		
		return $internalData;
	}
	
	
	
}
?>