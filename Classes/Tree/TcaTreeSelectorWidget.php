<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Michael Knoll <mimi@kaktusteam.de>
*           Daniel Lienert <daniel@lienert.cc>
*           
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
 * Class implements TCA tree selector widget that can be rendered within a TCE form
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Tree_TcaTreeSelectorWidget {

    /**
     * Fluid Renderer
     * @var Tx_Fluid_View_TemplateView
     */
    protected $fluidRenderer = NULL;



    /**
     * Extbase Object Manager
     * @var Tx_Extbase_Object_ObjectManager
     */
    protected $objectManager;



    /**
     * Holds all parameters as passed in from TCA
     * @var array
     */
    protected $tcaParameters;



    /**
     * Holds Plugin name for which this widget should be rendered
     * @var string
     */
    protected $pluginName;



    /**
     * Holds extension name for which this widget should be rendered
     * @var string
     */
    protected $extensionName;



    /**
     * Holds template path for fluid tca widget template
     * @var string
     */
    protected $templatePath;



    /**
     * User function to render TCA selector
     *
     * @param array $parameters
     * @param null $fObj
     */
    public function renderTcaTreeSelectorWidget(array $parameters=array(), $fObj=null) {
        $this->tcaParameters = $parameters;
        #echo "Parameters: <pre>" . print_r($this->tcaParameters, true) . "</pre>";
        $this->init();
        $this->addJsAndCssIncludes();
        return $this->fluidRenderer->render();
    }



    /**
     * Init the extbase Context and the configurationBuilder
     *
     * @param integer $pid
     * @throws Exception
     */
    protected function init() {
        $this->initPropertiesFromParamsArray();
        $this->initObjectManager();
        $this->initFluidRenderer();
        $this->initTemplate();
    }



    /**
     * Initializes properties from params array
     */
    protected function initPropertiesFromParamsArray() {
        // Make sure, parameters exist and throw Exceptions, if not
        $fieldConfigParameters = $this->tcaParameters['fieldConf']['config']['parameters'];
        $this->extensionName = $fieldConfigParameters['extensionName'];
        $this->pluginName = $fieldConfigParameters['pluginName'];
        $this->treeNamespace = $fieldConfigParameters['treeNamespace'];
        $this->templatePath = $fieldConfigParameters['templatePath'];
    }



    /**
     * Initializes Extbase object manager
     */
    protected function initObjectManager() {
        $this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
    }



    /**
     * Build A Fluid Renderer
     */
    protected function initFluidRenderer() {
        if(!$this->fluidRenderer) {

            /* @var $request Tx_Extbase_MVC_Request */
            $request = $this->objectManager->get('Tx_Extbase_MVC_Request');
            $request->setControllerExtensionName($this->extensionName);
            $request->setPluginName($this->pluginName);

            $this->fluidRenderer = $this->objectManager->get('Tx_Fluid_View_TemplateView');
            $controllerContext = $this->objectManager->get('Tx_Extbase_MVC_Controller_ControllerContext');
            $controllerContext->setRequest($request);
            $this->fluidRenderer->setControllerContext($controllerContext);

            $this->fluidRenderer->assign('treeNamespace', $this->treeNamespace);
        }
    }



    /**
     * Initializes Template in Fluid renderer
     */
    protected function initTemplate() {
        $fullQualifiedTemplatePath = t3lib_div::getFileAbsFileName($this->templatePath);
        $this->fluidRenderer->setTemplatePathAndFilename($fullQualifiedTemplatePath);
    }



    /**
     * Sets includes for CSS and JS
     */
    protected function addJsAndCssIncludes() {
        $doc = $this->getDocInstance();

        $pageRenderer = $doc->getPageRenderer();

        $compress = TRUE;

        // ExtJs
        #$pageRenderer->addJsFile('typo3/contrib/extjs/adapter/ext/ext-base.js', 'text/javascript', $compress);
        #$pageRenderer->addJsFile('fileadmin/ext-3.4.0/ext-all-debug-w-comments.js', 'text/javascript', $compress);

        $pageRenderer->addCssFile('fileadmin/ext-3.4.0/resources/css/ext-all.css', 'all', '', $compress);
    }


    /**
    * Gets instance of template if exists or create a new one.
    * Saves instance in viewHelperVariableContainer
    *
    * @return template $doc
    */
    protected function getDocInstance() {
        if (!isset($GLOBALS['SOBE']->doc)) {
            $GLOBALS['SOBE']->doc = t3lib_div::makeInstance('template');
            $GLOBALS['SOBE']->doc->backPath = $GLOBALS['BACK_PATH'];
        }
        return $GLOBALS['SOBE']->doc;
    }

}
?>