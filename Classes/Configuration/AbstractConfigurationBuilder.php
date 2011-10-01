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
 * Class implements abstract configuration builder
 *
 * @package ConfigurationBuilder
 * @author Michael Knoll 
 * @author Daniel Lienert 
 */
abstract class Tx_PtExtbase_Configuration_AbstractConfigurationBuilder {

	/**
	 * Holds configuration for plugin / extension
	 *
	 * @var array
	 */
	protected $settings;


	/**
	 * Holds definition of configuration object instances
	 *
	 * objectName
	 *  => factory = Classname of the factory
	 *  => tsKey typoscript key if diferent from objectName
	 *  => prototype = path to the prototype settings
	 *
	 * @var array
	 */
	protected $configurationObjectSettings = array();

	

	/**
	 * Chache for all configuration Objects
	 *
	 * @var unknown_type TODO: define a interface
	 */
	protected $configurationObjectInstances = array();


	
	/**
	 * Constructor for configuration builder.
	 *
	 * @param array $settings Configuration settings
	 */
	public function __construct(array $settings = array()) {
		$this->settings = $settings;
	}



	/**
	 * Magic functions
	 *
	 * @param string $name Name of method called
	 * @param array $arguments Arguments passed to called method
	 */
	public function __call($functionName, $arguments) {
		#$functionName = strtolower($functionName);
		
		$matches = array();
		$pattern = '/(get|build)(.+)Config(uration)?/is';
		preg_match($pattern, $functionName, $matches);

		if ($matches[2]) {
			if (false === function_exists('lcfirst')) {
				$matches[2][0] = strtolower($matches[2][0]);
			} else {
				$matches[2] = lcfirst($matches[2]); // PHP 5.3 only ;)
			}
			return $this->buildConfigurationGeneric($matches[2]);
		}
		Throw new Exception('The method configurationBuilder::' . $functionName . ' could not be found or handled by magic function. 1289407912');
	}


	
	/**
	 * Generic factory method for configuration objects
	 *
	 * @param string $configurationName
	 * @return mixed
	 */
	protected function buildConfigurationGeneric($configurationName) {

		if(!$this->configurationObjectInstances[$configurationName]) {

			if(!array_key_exists($configurationName, $this->configurationObjectSettings)) {
				throw new Exception('No Configuration Object with name ' . $configurationName . ' defined in ConfigurationBuilder 1289397150');
			}

			$factoryClass = $this->configurationObjectSettings[$configurationName]['factory'];

			if(!class_exists($factoryClass)) {
				Throw new Exception('Factory class for configuration ' . $configurationName . ': ' . $factoryClass .  'not found! 1293416866');
			}
			
			//$this->configurationObjectInstances[$configurationName] = $factoryClass::getInstance($this); // PHP 5.3 only ;)
			$this->configurationObjectInstances[$configurationName] = call_user_func(array($factoryClass, 'getInstance'), $this); // Avoid :: notation in PHP < 5.3

		}
		return $this->configurationObjectInstances[$configurationName];
	}
	


	/**
	 *
	 *
	 * @param string $configurationName
	 */
	public function getSettingsForConfigObject($configurationName) {
		if(!array_key_exists($configurationName, $this->configurationObjectSettings)) {
			throw new Exception('No Configuration Object with name ' . $configurationName . ' defined in ConfigurationBuilder 1289397150');
		}

		$tsKey = array_key_exists('tsKey', $this->configurationObjectSettings[$configurationName]) ? $this->configurationObjectSettings[$configurationName]['tsKey'] : $configurationName;
		if($tsKey) {
			$settings = array_key_exists($tsKey, $this->settings) ? $this->settings[$tsKey] : array();
		} else {
			$settings = $this->settings;
		}

		if(array_key_exists('prototype', $this->configurationObjectSettings[$configurationName])) {
			$settings = $this->getMergedSettingsWithPrototype($settings, $this->configurationObjectSettings[$configurationName]['prototype']);
		}

		return $settings;
	}
	


	/**
	 * Return the list specific settings merged with prototype settings
	 *
	 * @param array $listSepcificConfig
	 * @param string $objectName
	 * @return array
	 */
	public function getMergedSettingsWithPrototype($listSepcificConfig, $objectPath) {
		// TODO cache this!
		if(!is_array($listSepcificConfig)) $listSepcificConfig = array();
			$mergedSettings = t3lib_div::array_merge_recursive_overrule(
            $this->getPrototypeSettingsForObject($objectPath),
			$listSepcificConfig
        );

        return $mergedSettings;
	}



    /**
     * return a slice from the prototype arrray for the given objectPath
     *
     * @param string $objectPath
     * @return array prototypesettings
     */
    public function getPrototypeSettingsForObject($objectPath) {

    	$protoTypeSettings = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($this->protoTypeSettings, $objectPath);

    	if(!is_array($protoTypeSettings)) {
    		$protoTypeSettings = array();
    	}

    	return $protoTypeSettings;
    }

    

    /**
     * Returns array of settings for current list configuration
     *
     * @return array
     */
    public function getSettings($key = NULL) {
    	if(!$key) {
        	return $this->settings;
        } else {
        	if(array_key_exists($key, $this->settings)) {
        		return $this->settings[$key];
            } else {
                return array();
            }
        }
    }

    

	/**
	 * Merges configuration of settings in namespace of list identifiert
	 * with settings from plugin.
	 *
	 * @param void
	 * @return void
	 */
	protected function mergeAndSetGlobalAndLocalConf() {
		$settingsToBeMerged = $this->origSettings;
		unset($settingsToBeMerged['listConfig']);
		if (is_array($this->origSettings['listConfig'][$this->listIdentifier])) {
			$mergedSettings = t3lib_div::array_merge_recursive_overrule(
	            $settingsToBeMerged,
	            $this->origSettings['listConfig'][$this->listIdentifier]
	        );
	        $this->settings = $mergedSettings;
		}
	}
	
}

?>