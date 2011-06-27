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
	
	
	
	public static function debug($target, $renderAs = 'html') {
		
		if(self::$debug === NULL) {
			self::$debug = new self();	
		}
		
		if(is_object($target)) {
			self::$debug->debugObject($target);	
		}
		
	}
	

	public function debugTarget($target) {
		
		$this->debugData['debugTime'] = date('H:i:s');
		$this->debugData['debugType'] = gettype($target); 
		
		switch (gettype($target)) {
			case 'array':
				break;
			case 'object':
				$this->debugObject($target);
				break;
			case 'resource':
				break;
			default:	
		}
		
		echo $this->renderDebugData($debugData);
	}
	
	
	protected function debugObject($object, $lazy) {
		
		
		$this->debugData['className'] = get_class($object);  
		
		//print_r($this->processRawObjectData($object));
		

	}
	
	
	protected function debugArray($array) {
		return nl2br(print_r($array,1),1);
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