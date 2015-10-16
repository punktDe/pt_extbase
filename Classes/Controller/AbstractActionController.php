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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract controller extending Extbase ActionController. 
 * 
 * Here is a list of features, available per default in this controller class:
 *    
 *    * Lifecycle manager is loaded and running
 * 
 *    * View can be set via TS. View has to be set in TS via:
 *      plugin.<plugin_key>.settings.controller.<Controller_Name_without_Controller>.<action_Name_without_Action>.view = ViewClassName
 *  
 *    * Template can be set via TS Template path can be configured in TS via
 *      plugin.<plugin_key>.settings.controller.<Controller_Name_Without_Controller>.<action_name_without_action>.template = full_path_to_template_with.html
 * 
 * 
 * @author Michael Knoll 
 * @author Daniel Lienert 
 * @package Controller
 */
abstract class Tx_PtExtbase_Controller_AbstractActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var Tx_PtExtbase_Lifecycle_Manager
     */
    protected $lifecycleManager;

    
    
    /**
     * Custom template Path and Filename
     * Has to be set before resolveView is called!
     * 
     * @var string
     */
    protected $templatePathAndFileName;
    
    
    
    /**
     * Constructor for all plugin controllers
     *
     * @param Tx_PtExtbase_Lifecycle_Manager $lifeCycleManager
     */
    public function __construct(Tx_PtExtbase_Lifecycle_Manager $lifeCycleManager)
    {
        $this->lifecycleManager = $lifeCycleManager;
        if (TYPO3_MODE == 'FE' && !$GLOBALS['TSFE']->beUserLogin) {
            $this->errorMethodName = 'productionErrorAction';
        }
        parent::__construct();
    }



    /**
     * @return string
     */
    protected function productionErrorAction()
    {
        $parentMessage = parent::errorAction();
        if (strlen($parentMessage)!= 0) {
            return 'Invalid action or parameter';
        }
    }


    
    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     * @return void
     */
    protected function setViewConfiguration(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        parent::setViewConfiguration($view);
        $this->setCustomPathsInView($view);
    }
    
    
    
    /**
     * Resolve the viewObjectname in the following order
     * 
     * 1. TS-defined
     * 2. Determined by Controller/Action/Format
     * 3. Extbase BaseView 
     * 
     * @throws Exception
     * @return string
     */
    protected function resolveViewObjectName()
    {
        $viewClassName = $this->resolveTsDefinedViewClassName();
        if (!$viewClassName) {
            $viewClassName = parent::resolveViewObjectName();
        }
        if (!$viewClassName) {
            $viewClassName = $this->getFallbackViewClassName();
        }

        return $viewClassName;
    }
    
    
    
    /**
     * Template method for setting fallback view class in extending Contorllers
     *
     * @return string Class name of view, that should be taken by default
     */
    protected function getFallbackViewClassName()
    {
        return 'Tx_PtExtbase_View_BaseView';
    }



    /**
     * Resolve the viewClassname defined via typoscript
     *
     * View class can be configured in TS via
     * plugin.<plugin_key>.settings.controller.<Controller_Name_without_Controller>.<action_Name_without_Action>.view = ViewClassName
     *
     * @throws Exception if view class does not exist
     * @return string
     */
    protected function resolveTsDefinedViewClassName()
    {
        $viewClassName = $this->getTsViewClassName();
        
        if ($viewClassName != '') {
            if (!class_exists($viewClassName)) {

                // Use the viewClassName as redirect path to a typoscript value holding the viewClassName
                $redirectedViewClassName = $viewClassName . '.viewClassName';
                $tsRedirectPath = explode('.', $redirectedViewClassName);
                $redirectedViewClassName = \TYPO3\CMS\Extbase\Utility\ArrayUtility::getValueByPath($this->settings, $tsRedirectPath);

                if ($redirectedViewClassName) {
                    $viewClassName = $redirectedViewClassName;
                }
            }
        }

        if ($viewClassName && !class_exists($viewClassName)) {
            throw new Exception('View class does not exist: ' . $viewClassName . ' 1281369758');
        }

        // TODO make sure that given class implements a view

        return $viewClassName;
    }
    
    
    
    /**
     * Template method for getting class name for view to be used in this controller from
     * TypoScript.
     * 
     * Overwrite this method in your extending controller to enable adding
     * further namespace settings etc.
     *
     * @return string View class name to be used in this controller
     */
    protected function getTsViewClassName()
    {
        return $this->settings['controller'][$this->request->getControllerName()][$this->request->getControllerActionName()]['view'];
    }
    
    
    
    /**
     * Set the TS defined custom paths in view
     * 
     * Template path can be configured in TS via
     * plugin.<plugin_key>.settings.controller.<Controller_Name_Without_Controller>.<action_name_without_action>.template = full_path_to_template_with.html
     * 
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     * @throws Exception
     */
    protected function setCustomPathsInView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        
        // We use template method here to enable adding further informations in extending controllers
        $templatePathAndFilename = $this->getTsTemplatePathAndFilename();
        
        // We have no template path set by TS --> fallback
        if (!$templatePathAndFilename) {
            $templatePathAndFilename = $this->templatePathAndFileName;
        }
        
        if (isset($templatePathAndFilename) && strlen($templatePathAndFilename) > 0) {
            // We enable FILE: and EXT: prefix for template path
            if (file_exists(GeneralUtility::getFileAbsFileName($templatePathAndFilename))) {
                $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templatePathAndFilename));
            } else {
                throw new Exception('Given template path and filename could not be found or resolved: ' . GeneralUtility::getFileAbsFileName($templatePathAndFilename), 1284655109);
            }
        }
    }
    
    
    
    /**
     * Template method for getting template path and filename from
     * TypoScript settings.
     * 
     * Overwrite this method in extending controllers to add further namespace conventions etc.
     *
     * @return string Template path and filename
     */
    protected function getTsTemplatePathAndFilename()
    {
        return $this->settings['controller'][$this->request->getControllerName()][$this->request->getControllerActionName()]['template'];
    }

    
    
    /**
     * Fires end-of-lifecycle signal if processing backend request.
     * 
     * @see processRequest() in parent
     */
    public function processRequest(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request, \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response)
    {
        parent::processRequest($request, $response);
        
        if (TYPO3_MODE === 'BE') {
            // if we are in BE mode, this ist the last line called
            $this->lifecycleManager->updateState(Tx_PtExtbase_Lifecycle_Manager::END);
        }
    }



    /**
     * Setter for view, enables injection of mock view for easy testing.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view View to be injected (for testing)
     */
    public function setView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        $this->view = $view;
    }



    /**
     * Recursively iterates through the specified arguments and turns instances of type \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
     * into an arrays containing the uid of the domain object.
     *
     * @param array $arguments The arguments to be iterated
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentValueException
     * @return array The modified arguments array
     */
    protected function convertDomainObjectsToIdentityArrays(array $arguments)
    {
        foreach ($arguments as $argumentKey => $argumentValue) {
            // if we have a LazyLoadingProxy here, make sure to get the real instance for further processing
            if ($argumentValue instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy) {
                $argumentValue = $argumentValue->_loadRealInstance();
                // also update the value in the arguments array, because the lazyLoaded object could be
                // hidden and thus the $argumentValue would be NULL.
                $arguments[$argumentKey] = $argumentValue;
            }
            if ($argumentValue instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject) {
                if ($argumentValue->getUid() !== null) {
                    $arguments[$argumentKey] = $argumentValue->getUid();
                } elseif ($argumentValue instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject) {
                    $arguments[$argumentKey] = $this->convertTransientObjectToArray($argumentValue);
                } else {
                    throw new \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentValueException('Could not serialize Domain Object ' . get_class($argumentValue) . '. It is neither an Entity with identity properties set, nor a Value Object.', 1260881688);
                }
            } elseif (is_array($argumentValue)) {
                $arguments[$argumentKey] = $this->convertDomainObjectsToIdentityArrays($argumentValue);
            }
        }
        return $arguments;
    }


    /**
     * Converts a given object recursively into an array.
     *
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject $object
     * @return array
     * @todo Refactore this into convertDomainObjectsToIdentityArrays()
     */
    public function convertTransientObjectToArray(\TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject $object)
    {
        $result = array();
        foreach ($object->_getProperties() as $propertyName => $propertyValue) {
            if ($propertyValue instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject) {
                if ($propertyValue->getUid() !== null) {
                    $result[$propertyName] = $propertyValue->getUid();
                } else {
                    $result[$propertyName] = $this->convertTransientObjectToArray($propertyValue);
                }
            } elseif (is_array($propertyValue)) {
                $result[$propertyName] = $this->convertDomainObjectsToIdentityArrays($propertyValue);
            } else {
                $result[$propertyName] = $propertyValue;
            }
        }
        return $result;
    }
}
