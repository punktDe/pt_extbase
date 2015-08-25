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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility to include defined frontend libraries as jQuery and related CSS
 *
 *
 * FE Usage (eID):
 * ===============
 *
 * If you want to use this dispatcher in FE, add following lines to ext_localconf:
 * $TYPO3_CONF_VARS['FE']['eID_include']['ptxAjax'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('pt_extbase').'Classes/Utility/eIDDispatcher.php';
 *
 * Use following URL for calling eid script:
 * http://pt_list_dev.centos.localhost/?eID=ptxAjax&extensionName=TnTests&pluginName=pi1&controllerName=Ajax&actionName=test
 *
 * ATTENTION: You cannot use this dispatcher directly in FE, as you need to initalize some objects first (Database, TSFE, fe_user...)
 * TODO we should have a builder class for generating a dispatcher object with the required functionality (compare to PicoBuilder->with(...))
 *
 *
 * BE Usage (ajax):
 * ================
 *
 * If you want to use this dispatcher in BE, add following lines to ext_localconf:
 * $TYPO3_CONF_VARS['BE']['AJAX']['ptxAjax'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('pt_extbase').'Classes/Utility/AjaxDispatcher.php:Tx_PtExtbase_Utility_AjaxDispatcher->initAndEchoDispatch';
 *
 * Use following URL for your ajax calls from backend:
 * http://pt_list_dev.centos.localhost/typo3/ajax.php?ajaxID=ptxAjax&extensionName=TnTests&pluginName=pi1&controllerName=Ajax&actionName=test
 *
 *
 * @package Utility
 * @author Daniel Lienert <daniel@lienert.cc>
 * @author Michael Knoll <mimi@kaktusteam.de>
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
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;


	/**
	 * @var string
	 */
	protected $vendorName;


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
	 * @var string
	 */
	protected $moduleSignature;


	/**
	 * @var array
	 */
	protected $dispatchCallArguments;


	/**
	 * @var string
	 */
	protected $format = 'html';


	/**
	 * Initializes dispatcher, dispatches request and echos it
	 */
	public function initAndEchoDispatch($pageUid = 0) {
		echo $this->initAndDispatch($pageUid);
	}


	/**
	 * Initializes and dispatches actions
	 *
	 * Call this function if you want to use this dispatcher "standalone"
	 */
	public function initAndDispatch($pageUid = 0) {
		$this->init($pageUid);

		$content = $this->dispatch();
		return $content;
	}


	/**
	 * Called by ajax.php / eID.php
	 * Builds an extbase context and returns the response
	 *
	 * ATTENTION: You should not call this method without initializing the dispatcher. Use initAndDispatch() instead!
	 */
	public function dispatch() {

		$this->dispatchCallArguments = func_get_args();
		$this->checkModuleAccessIfInBackend();
		$this->checkAllowedControllerActions();

		$configuration['extensionName'] = $this->extensionName;
		$configuration['pluginName'] = $this->pluginName;
		if($this->vendorName) $configuration['vendorName'] = $this->vendorName;

		$bootstrap = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Core\Bootstrap');
		$bootstrap->initialize($configuration);

		$this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		$request = $this->buildRequest();
		$response = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Web\Response'); /** @var  $response \TYPO3\CMS\Extbase\Mvc\Web\Response */

		$dispatcher = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Dispatcher'); /** @var \TYPO3\CMS\Extbase\Mvc\Dispatcher $dispatcher */
		$dispatcher->dispatch($request, $response);

		$response->sendHeaders();
		$content = $response->getContent();
		return $content;
	}


	/**
	 * Use the ajaxID to determine the target module and check the users access on that module
	 *
	 * @throws Exception
	 */
	protected function checkModuleAccessIfInBackend() {
		if (TYPO3_MODE === 'BE') {
			if (is_array($this->dispatchCallArguments) && $this->dispatchCallArguments[1] instanceof \TYPO3\CMS\Core\Http\AjaxRequestHandler) {
				$ajaxId = $this->dispatchCallArguments[1]->getAjaxID();
				if (!stristr($ajaxId, '::')) throw new \Exception('Please name the ajaxId the following way: TargetModuleSignature::IndividualAJAXIdentifier. The current ajax ID is: ' . $ajaxId, 1391143615);
				list($moduleSignature) = explode('::', $ajaxId);

				$backendUser = $GLOBALS['BE_USER'];
				/** @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication $backendUser */
				$backendUser->modAccess(array('name' => $moduleSignature, 'access' => array('user', 'group')), TRUE);
			}
		}
	}


	/**
	 * Check if the requested action is marked as accessible
	 *
	 * @throws Exception
	 */
	protected function checkAllowedControllerActions() {
		if (!$this->extensionName || !$this->controllerName || !$this->actionName) throw new \Exception('Either extension, controller or action is undefined.', 1391146166);

		$nameSpace = implode('.', array('TYPO3_CONF_VARS.EXTCONF.pt_extbase.ajaxDispatcher.apiConfiguration', $this->extensionName, $this->controllerName, 'allowedControllerActions'));
		$allowedControllerActions = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($GLOBALS, $nameSpace);

		if (!(in_array($this->actionName, $allowedControllerActions) || $this->checkLegacyAllowedControllerActions())) {
			throw new \Exception('The requested controller / action is not allowed to be called via ajax / eId. You have to grant the access with the configuration: $GLOBALS[\'' . str_replace('.', "']['", $nameSpace) . "'][] = '" . $this->actionName . "'; in your ext_localconf.php", 1391145113);
		}


	}


	/**
	 * @param integer $pageUid
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function init($pageUid = 0) {
		$this->initCallArguments();

		if ($pageUid !== 0) {
			$this->pageUid = $pageUid;
		} else {
			$this->pageUid = $this->getPageUidFromConfiguration();
		}

		$this->initTca()
			->initTsfe($this->pageUid)
			->initFeUser();

		return $this;
	}


	/**
	 * @return $this
	 */
	public function initTca() {
		\TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();

		return $this;
	}


	/**
	 * @param $pageUid
	 * @return $this
	 */
	public function initTsfe($pageUid = NULL) {

		$GLOBALS['TSFE'] = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], $pageUid, '0', 1, '', '', '', '');
		$GLOBALS['TSFE']->sys_page = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Page\PageRepository');

		return $this;
	}


	/**
	 * @return $this
	 */
	public function initFeUser() {
		$GLOBALS['TSFE']->initFEuser();

		return $this;
	}


	/**
	 * @return $this
	 */
	public function initFeUserGroups() {
		$GLOBALS['TSFE']->initUserGroups();
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
		$this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager')->persistAll();
		$this->objectManager->get('TYPO3\CMS\Extbase\Reflection\ReflectionService')->shutdown();
	}


	/**
	 * Build a request object
	 *
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Request $request
	 */
	protected function buildRequest() {
		$request = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Web\Request');
		/* @var $request \TYPO3\CMS\Extbase\Mvc\Request */
		$request->setControllerExtensionName($this->extensionName);
		$request->setPluginName($this->pluginName);
		$request->setControllerName($this->controllerName);
		$request->setControllerActionName($this->actionName);
		$request->setArguments($this->arguments);
		$request->setFormat($this->format);
		$request->setControllerVendorName($this->vendorName);

		return $request;
	}


	/**
	 * Prepare the call arguments
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function initCallArguments() {
		$request = GeneralUtility::_GP('request');

		if ($request) {
			$this->setRequestArgumentsFromJSON($request);
		} else {
			$this->setRequestArgumentsFromGetPost();
		}

		$this->setVendorAndExtensionName();

		$this->pluginName = $this->requestArguments['pluginName'];
		$this->controllerName = $this->requestArguments['controllerName'];
		$this->actionName = $this->requestArguments['actionName'];

		$this->arguments = $this->requestArguments['arguments'];
		if (!is_array($this->arguments)) $this->arguments = array();

		return $this;
	}


	protected function setVendorAndExtensionName() {
		$vendorName = NULL;

		$this->extensionName = $this->requestArguments['extensionName'];

		$delimiterPosition = strrpos($this->extensionName, '.');
		if ($delimiterPosition !== FALSE) {
			$this->vendorName = str_replace('.', '\\', substr($this->extensionName, 0, $delimiterPosition));
			$this->extensionName = substr($this->extensionName, $delimiterPosition + 1);
		}
	}


	/**
	 * Set the request array from JSON
	 *
	 * @param string $request
	 */
	protected function setRequestArgumentsFromJSON($request) {
		$requestArray = json_decode($request, true);
		if (is_array($requestArray)) {
			\TYPO3\CMS\Extbase\Utility\ArrayUtility::mergeRecursiveWithOverrule($this->requestArguments, $requestArray);
		}
	}


	/**
	 * Set the request array from the getPost array
	 */
	protected function setRequestArgumentsFromGetPost() {
		$validArguments = array('extensionName', 'pluginName', 'controllerName', 'actionName', 'arguments');
		foreach ($validArguments as $argument) {
			if (GeneralUtility::_GP($argument)) $this->requestArguments[$argument] = GeneralUtility::_GP($argument);
		}
	}


	/**
	 * @param $extensionName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 * @throws Exception
	 */
	public function setExtensionName($extensionName) {
		if (!$extensionName) throw new Exception('No extension name set for extbase request.', 1327583056);

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


	/**
	 * @param $vendorName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setVendorName($vendorName) {
		$this->vendorName = $vendorName;
		return $this;
	}


	/**
	 * @param string $format
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setFormat($format) {
		$this->format = $format;
		return $this;
	}

	/**
	 * @throws Exception
	 */
	protected function checkLegacyAllowedControllerActions() {
		$nameSpace = implode('.', array('TYPO3_CONF_VARS.EXTCONF.pt_extbase.ajaxDispatcher.allowedControllerActions', $this->extensionName, $this->controllerName, $this->actionName));
		return Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($GLOBALS, $nameSpace);
	}

	/**
	 * @return integer
	 */
	protected function getPageUidFromConfiguration() {
		$nameSpace = implode('.', array('TYPO3_CONF_VARS.EXTCONF.pt_extbase.ajaxDispatcher.apiConfiguration', $this->extensionName, $this->controllerName, 'startingPoint'));
		return Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($GLOBALS, $nameSpace);
	}

}
