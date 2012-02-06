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
* Utility to include defined frontend libraries as jQuery and related CSS
*
*
* @package Utility
* @author Daniel Lienert <daniel@lienert.cc>
*/

class Tx_PtExtbase_Utility_AjaxDispatcher {

	/**
	 * Array of all request Arguments
	 *
	 * @var array
	 */
	protected $requestArguments = array();



	/**
	 * Extbase Object Manager
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;



	/**
	 * @var string
	 */
	protected $extensionName;



	/**
	 * @var string
	 */
	protected $pluginName;



	/**
	 * @var string
	 */
	protected $controllerName;



	/**
	 * @var string
	 */
	protected $actionName;



	/**
	 * @var array
	 */
	protected $arguments = array();



	/**
	 * @var integer
	 */
	protected $pageUid;



    /**
     * Initializes and dispatches actions
     *
     * Call this function if you want to use this dispatcher "standalone"
     */
    public function initAndDispatch() {
        $this->initCallArguments()->dispatch();
    }



    /**
     * Called by ajax.php / eID.php
     * Builds an extbase context and returns the response
     *
     * ATTENTION: You should not call this method without initializing the dispatcher. Use initAndDispatch() instead!
     */
    public function dispatch() {
        $configuration['extensionName'] = $this->extensionName;
        $configuration['pluginName'] = $this->pluginName;

        $bootstrap = t3lib_div::makeInstance('Tx_Extbase_Core_Bootstrap');
        $bootstrap->initialize($configuration);

        $this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');

        $request = $this->buildRequest();
        $response = $this->objectManager->create('Tx_Extbase_MVC_Web_Response');

        $dispatcher =  $this->objectManager->get('Tx_Extbase_MVC_Dispatcher');
        $dispatcher->dispatch($request, $response);

        $response->sendHeaders();
        return $response->getContent();
    }


	
	/**
	 * @param null $pageUid
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function init($pageUid = NULL) {
		#define('TYPO3_MODE','FE');

		$this->pageUid = $pageUid;

		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $TYPO3_CONF_VARS, $pageUid, '0', 1, '', '','','');
		$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');

		#$GLOBALS['TSFE']->initFeuser();
		$GLOBALS['TSFE']->fe_user = tslib_eidtools::initFeUser();

		return $this;
	}



	/**
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function initTypoScript() {
		$GLOBALS['TSFE']->getPageAndRootline();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();

		return $this;
	}



	/**
	 * @return void
	 */
    public function cleanShutDown() {
        $this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();
        $this->objectManager->get('Tx_Extbase_Reflection_Service')->shutdown();
    }



    /**
     * Build a request object
     *
     * @return Tx_Extbase_MVC_Web_Request $request
     */
    protected function buildRequest() {
        $request = $this->objectManager->get('Tx_Extbase_MVC_Web_Request'); /* @var $request Tx_Extbase_MVC_Request */
        $request->setControllerExtensionName($this->extensionName);
        $request->setPluginName($this->pluginName);
        $request->setControllerName($this->controllerName);
        $request->setControllerActionName($this->actionName);
        $request->setArguments($this->arguments);

        return $request;
    }



	/**
	 * Prepare the call arguments
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function initCallArguments() {
		$request = t3lib_div::_GP('request');

		if ($request) {
			$this->setRequestArgumentsFromJSON($request);
		} else {
			$this->setRequestArgumentsFromGetPost();
		}

		$this->extensionName  = $this->requestArguments['extensionName'];
		$this->pluginName = $this->requestArguments['pluginName'];
		$this->controllerName = $this->requestArguments['controllerName'];
		$this->actionName = $this->requestArguments['actionName'];

		$this->arguments = $this->requestArguments['arguments'];

		error_log(print_r($this->requestArguments,1));

		if (!is_array($this->arguments)) $this->arguments = array();

		return $this;
	}



    /**
     * Set the request array from JSON
     *
     * @param string $request
     */
    protected function setRequestArgumentsFromJSON($request) {
        $requestArray = json_decode($request, true);
        if(is_array($requestArray)) {
            $this->requestArguments = t3lib_div::array_merge_recursive_overrule($this->requestArguments, $requestArray);
        }
    }



    /**
     * Set the request array from the getPost array
     */
    protected function setRequestArgumentsFromGetPost() {
        $validArguments = array('extensionName','pluginName','controllerName','actionName','arguments');
        foreach($validArguments as $argument) {
            if(t3lib_div::_GP($argument)) $this->requestArguments[$argument] = t3lib_div::_GP($argument);
        }
    }


	
	/**
	 * @param $extensionName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setExtensionName($extensionName) {
		if(!$extensionName) throw new Exception('No extension name set for extbase request.', 1327583056);

		$this->extensionName = $extensionName;
		return $this;
	}



	/**
	 * @param $pluginName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setPluginName($pluginName) {
		$this->pluginName = $pluginName;
		return $this;
	}



	/**
	 * @param $controllerName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setControllerName($controllerName) {
		$this->controllerName = $controllerName;
		return $this;
	}



	/**
	 * @param $actionName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setActionName($actionName) {
		$this->actionName = $actionName;
		return $this;
	}

}
?>