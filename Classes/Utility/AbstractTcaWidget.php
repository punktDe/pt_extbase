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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract class holding main functionality for TCA widgets based on Extbase and Fluid
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */

class Tx_PtExtbase_Utility_AbstractTcaWidget {

    /**
     * Fluid Renderer
     * @var Tx_Fluid_View_TemplateView
     */
    protected $fluidRenderer = NULL;



    /**
     * Extbase Object Manager
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
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
     * Holds current record (record that is displayed in form where tree widget is rendered)
     * @var array
     */
    protected $row;



    /**
     * Holds instance of t3lib_TCEforms object passed to this widget.
     * @var t3lib_TCEforms
     */
    protected $tceForms;



    /**
     * If set to true, corresponding configuration in TCA is many-to-many field (M:N)
     * @var boolean
     */
    protected $isManyToManyField;



    /**
     * If set to true, corresponding configuration in TCA is one-to-many field (1:N)
     * @var boolean
     */
    protected $is1ToManyField;



    /**
     * Field name of tce-forms-persisted field in rendered form
     *
     * When form in which this widget is rendered is saved,
     * the value of this field will be persisted by tce_forms engine.
     *
     * @var string
     */
    protected $formFieldName;



    /**
     * Holds form field value (itemFormElValue in $PA array)
     *
     * Syntax is UID1|Description1,UID2|Description2, ...
     *
     * @var string
     */
    protected $formFieldValue;



    /**
     * Initialize widget settings and variables
     *
     * @param array $parameters Parameters passed by TCA rendering call
     * @param t3lib_TCEforms $fobj t3lib_TCEforms object passed by TCA rendering call
     */
    protected function init(array $params = array(), \TYPO3\CMS\Backend\Form\FormEngine $fobj = null) {
        $this->tcaParameters = $params;
        $this->tceForms = $fobj;
        $this->initPropertiesFromParamsArray();
        $this->initFrameWork();
        $this->initFluidRenderer();
        $this->initTemplate();
        $this->assignVariablesToView();
    }



    /**
     * Initializes properties from params array
     */
    protected function initPropertiesFromParamsArray() {
        $this->throwExceptionIfFiedConfigParametersAreNotSet();

        $fieldConfigParameters = $this->tcaParameters['fieldConf']['config']['parameters'];
        $this->extensionName = $fieldConfigParameters['extensionName'];
        $this->pluginName = $fieldConfigParameters['pluginName'];
        $this->treeNamespace = $fieldConfigParameters['treeNamespace'];

        if (array_key_exists('templatePath', $fieldConfigParameters) && $fieldConfigParameters['templatePath'] != '') {
            $this->templatePath = $fieldConfigParameters['templatePath'];
        }

        if (array_key_exists('maxitems', $this->tcaParameters['fieldConf']['config']) && $this->tcaParameters['fieldConf']['config']['maxitems'] > 0) {
            // we have M:N field
            $this->isManyToManyField = true;
            $this->is1ToManyField = false;
        } else {
            // we have 1:M field
            $this->is1ToManyField = true;
            $this->isManyToManyField = false;
        }

        $this->formFieldName = $this->tcaParameters['itemFormElName'];
        $this->formFieldValue = $this->tcaParameters['itemFormElValue'];

        $this->table = $this->tcaParameters['table'];
        $this->field = $this->tcaParameters['field'];
        $this->row = $this->tcaParameters['row'];
    }



    /**
     * Throws exception, if requested TCA configuration parameters [fieldConfig][config][parameters] is not set in TCA configuration for field.
     *
     * @throws Exception
     */
    protected function throwExceptionIfFiedConfigParametersAreNotSet() {
        if (!array_key_exists('fieldConf', $this->tcaParameters)) {
            throw new Exception('Cannot use ' . get_class($this) . ' without having fieldConfig set in corresponding TCA configuration! 1328299642');
        }

        if (!array_key_exists('config', $this->tcaParameters['fieldConf'])) {
            throw new Exception('Cannot use ' . get_class($this) . ' without having fieldConfig[config] set in corresponding TCA configuration! 1328299643');
        }

        if (!array_key_exists('parameters', $this->tcaParameters['fieldConf']['config'])) {
            throw new Exception('Cannot use ' . get_class($this) . ' without having fieldConfig[config][parameters] set in corresponding TCA configuration! 1328299644');
        }
    }



    /**
     * Initializes Extbase object manager
     */
    protected function initFrameWork() {
		 $this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		 $bootstrap = $this->objectManager->get('TYPO3\CMS\Extbase\Core\Bootstrap');
		 $bootstrap->initialize(array('extensionName' => $this->extensionName, 'pluginName' => $this->pluginName));
    }


    /**
     * Initialize Fluid Renderer (which is a Fluid view)
     */
    protected function initFluidRenderer() {
        if(!$this->fluidRenderer) {
            $request = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Request'); /* @var $request \TYPO3\CMS\Extbase\Mvc\Request */
            $request->setControllerExtensionName($this->extensionName);
            $request->setPluginName($this->pluginName);

            $this->fluidRenderer = $this->objectManager->get('TYPO3\CMS\Fluid\View\TemplateView');
            $controllerContext = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext');
            $controllerContext->setRequest($request);
            $this->fluidRenderer->setControllerContext($controllerContext);
        }
    }



    /**
     * Returns array of form
     *
     * array ( $uid => $label )
     *
     * of selected values. Uid is node uid, label is node label.
     *
     * @return array
     */
    protected function getSelectedValues() {
        $listOfValues = explode(',', $this->formFieldValue);
        $selectedValuesArray = array();
        foreach ($listOfValues as $singleValue) {
            list($uid, $label) = explode('|', $singleValue);
            $selectedValuesArray[$uid] = urldecode($label);
        }
        return $selectedValuesArray;
    }



    /**
     * Initializes Template in Fluid renderer
     */
    protected function initTemplate() {
        $fullQualifiedTemplatePath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->templatePath);
        $this->fluidRenderer->setTemplatePathAndFilename($fullQualifiedTemplatePath);
    }



    /**
    * Gets instance of template if exists or create a new one.
    * Saves instance in viewHelperVariableContainer
    *
    * @return template $doc
    */
    protected function getDocInstance() {
        if (!isset($GLOBALS['SOBE']->doc)) {
            $GLOBALS['SOBE']->doc = GeneralUtility::makeInstance('TYPO3\CMS\Backend\Template\DocumentTemplate');
            $GLOBALS['SOBE']->doc->backPath = $GLOBALS['BACK_PATH'];
        }
        return $GLOBALS['SOBE']->doc;
    }

}