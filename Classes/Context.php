<?php
namespace PunktDe\PtExtbase;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;

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
* Holds the extbaseContext of the current plugin instance
*
* @package Extbase
* @author Daniel Lienert
*/

class Context implements ContextInterface
{
    /**
     * @var ControllerContext
     */
    protected $controllerContext;
    
    
    /**
     * @var bool;
     */
    protected $inCachedMode = false;
    
    
    /**
     * Namepsace of current Extension
     * 
     * @var string
     */
    protected $extensionName;
    
    
    /**
     * @var string
     */
    protected $extensionNameSpace;

        
    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;
    
    
    
    /**
     * Initialize the object (called by objectManager)
     * 
     */
    public function initializeObject()
    {
        $frameWorkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        
        $this->extensionName = $frameWorkConfiguration['extensionName'];
        $this->extensionNameSpace = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')
            ->get('TYPO3\\CMS\\Extbase\\Service\\ExtensionService')
            ->getPluginNamespace($frameWorkConfiguration['extensionName'], $frameWorkConfiguration['pluginName']);
        
        $this->inCachedMode = $frameWorkConfiguration['pluginName'] == 'Cached' ? true : false;
        
        unset($frameWorkConfiguration);
    }
    
    
    
    /**
     * @param ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }
    
    
    
    /**
     * Set the Controller Context
     * 
     * @param ControllerContext $controllerContext
     */
    public function setControllerContext(ControllerContext $controllerContext)
    {
        $this->controllerContext = $controllerContext;
    }
    
    
    
    /**
     * @return ControllerContext $controllerContext
     */
    public function getControllerContext()
    {
        return $this->controllerContext;
    }
    
    
    
    /**
     * @return bool
     */
    public function isInCachedMode()
    {
        return $this->inCachedMode;
    }
    
    
    
    /**
     * Set the cached mode for the complete extension.
     * This is autmatically set when extlsit is used as standalone cached extension
     * 
     * @param bool $inCachedMode
     */
    public function setInCachedMode($inCachedMode)
    {
        $this->inCachedMode = $inCachedMode;
    }
    
    
    
    /**
     * @return string
     */
    public function getExtensionNamespace()
    {
        return $this->extensionNameSpace;
    }

    
    
    /**
     * @return string
     */
    public function getExtensionName()
    {
        return $this->extensionName;
    }
}
