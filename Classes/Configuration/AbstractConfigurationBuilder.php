<?php
namespace PunktDe\PtExtbase\Configuration;

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

use PunktDe\PtExtbase\Utility\NamespaceUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Class implements abstract configuration builder
 */
abstract class AbstractConfigurationBuilder implements ConfigurationBuilderInterface
{
    /**
     * Holds configuration for plugin / extension
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Prototype settings for ts-configurable objects
     * @var array
     */
    protected $prototypeSettings = [];

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
    protected $configurationObjectSettings = [];

    /**
     * Chache for all configuration Objects
     *
     * @var array TODO: define a interface
     */
    protected $configurationObjectInstances = [];

    /**
     * Constructor for configuration builder.
     *
     * @param array $settings Configuration settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * Magic functions
     *
     * @param string $functionName Name of method called
     * @param array $arguments Arguments passed to called method
     * @return mixed
     * @throws \Exception
     */
    public function __call($functionName, $arguments)
    {
        $matches = [];
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
        throw new \Exception('The method configurationBuilder::' . $functionName . ' could not be found or handled by magic function.', 1289407912);
    }
    
    /**
     * Generic factory method for configuration objects
     *
     * @param string $configurationName
     * @param mixed $parameters
     * @return mixed
     * @throws \Exception
     */
    protected function buildConfigurationGeneric($configurationName, $parameters = [])
    {
        if (!$this->configurationObjectInstances[$configurationName]) {
            if (!array_key_exists($configurationName, $this->configurationObjectSettings)) {
                throw new \Exception('No Configuration Object with name ' . $configurationName . ' defined in ConfigurationBuilder', 1289397150);
            }

            $factoryClass = $this->configurationObjectSettings[$configurationName]['factory'];

            if (!class_exists($factoryClass)) {
                throw new \Exception('Factory class for configuration ' . $configurationName . ': ' . $factoryClass .  'not found!', 1293416866);
            }
            
            $this->configurationObjectInstances[$configurationName] = call_user_func([$factoryClass, 'getInstance'], $this, $parameters); // Avoid :: notation in PHP < 5.3
        }
        return $this->configurationObjectInstances[$configurationName];
    }

    /**
     * @param string $configurationName
     * @return array
     * @throws \Exception
     */
    public function getSettingsForConfigObject($configurationName)
    {
        if (!array_key_exists($configurationName, $this->configurationObjectSettings)) {
            throw new \Exception('No Configuration Object with name ' . $configurationName . ' defined in ConfigurationBuilder', 1289397150);
        }

        $tsKey = array_key_exists('tsKey', $this->configurationObjectSettings[$configurationName]) ? $this->configurationObjectSettings[$configurationName]['tsKey'] : $configurationName;
        if ($tsKey) {
            $settings = array_key_exists($tsKey, $this->settings) ? $this->settings[$tsKey] : [];
        } else {
            $settings = $this->settings;
        }

        if (array_key_exists('prototype', $this->configurationObjectSettings[$configurationName])) {
            $settings = $this->getMergedSettingsWithPrototype($settings, $this->configurationObjectSettings[$configurationName]['prototype']);
        }

        return $settings;
    }
    
    /**
     * Return the specific settings merged with prototype settings
     *
     * @param array $overwriteSettings
     * @param string $objectPath
     * @return array
     */
    public function getMergedSettingsWithPrototype($overwriteSettings, $objectPath)
    {
        // TODO cache this!

        if (!is_array($overwriteSettings)) {
            $overwriteSettings = [];
        }

        $mergedSettings = $this->getPrototypeSettingsForObject($objectPath);

        ArrayUtility::mergeRecursiveWithOverrule($mergedSettings, $overwriteSettings);

        return $mergedSettings;
    }

    /**
     * return a slice from the prototype arrray for the given objectPath
     *
     * @param string $objectPath
     * @return array prototype settings for given object path
     */
    public function getPrototypeSettingsForObject($objectPath)
    {
        $prototypeSettings = NamespaceUtility::getArrayContentByArrayAndNamespace($this->prototypeSettings, $objectPath);

        if (!is_array($prototypeSettings)) {
            $prototypeSettings = [];
        }

        return $prototypeSettings;
    }

    /**
     * Returns array of settings for current list configuration
     *
     * @param string $key
     * @return array
     */
    public function getSettings($key = '')
    {
        if ($key != '') {
            return NamespaceUtility::getArrayContentByArrayAndNamespace($this->settings, $key);
        } else {
            return $this->settings;
        }
    }
}
