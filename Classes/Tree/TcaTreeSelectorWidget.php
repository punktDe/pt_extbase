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
class Tx_PtExtbase_Tree_TcaTreeSelectorWidget extends Tx_PtExtbase_Utility_AbstractTcaWidget {

    /**
     * Holds template path for fluid tca widget template
     * @var string
     */
    protected $templatePath = 'EXT:pt_extbase/Resources/Private/Templates/Tree/Tca.html';



    /**
     * Holds class name for node repository
     *
     * null means, we use default node repository set in tree repository builder
     *
     * @var string
     */
    protected $nodeRepositoryClassName = null;



    /**
     * User function to render TCA selector
     *
     * @param array $parameters
     * @param null $fObj
     */
    public function renderTcaTreeSelectorWidget(array $parameters=array(), $fObj=null) {
        $this->init($parameters, $fObj);
        $this->addJsAndCssIncludes();

        return $this->fluidRenderer->render();
    }



    /**
     * Initialize widget settings and variables
     *
     * @param array $parameters Parameters passed by TCA rendering call
     * @param t3lib_TCEforms $fobj t3lib_TCEforms object passed by TCA rendering call
     */
    protected function init($parameters = array(), t3lib_TCEforms $fobj = null) {
        parent::init($parameters, $fobj);
        $this->initTreeRepositoryBuilder();
    }



    /**
     * Initializes properties from params array
     */
    protected function initPropertiesFromParamsArray() {
        parent::initPropertiesFromParamsArray();
        $fieldConfigParameters = $this->tcaParameters['fieldConf']['config']['parameters'];
        if (array_key_exists('nodeRepositoryClassName', $fieldConfigParameters) && $fieldConfigParameters['nodeRepositoryClassName'] != '') {
            $this->nodeRepositoryClassName = $fieldConfigParameters['nodeRepositoryClassName'];
        }
    }



    /**
     * Sets variables in fluid template
     */
    protected function assignVariablesToView() {
        $this->fluidRenderer->assign('nodeRepositoryClassName', $this->nodeRepositoryClassName);
        $this->fluidRenderer->assign('treeNamespace', $this->treeNamespace);
        $this->fluidRenderer->assign('formFieldName', $this->formFieldName);
        $this->fluidRenderer->assign('selectedValues', $this->getSelectedValues());
        $this->fluidRenderer->assign('selectedValuesCommaSeparated', implode(',', array_keys($this->getSelectedValues())));
       // $this->fluidRenderer->assign('debug', "Parameters: <pre>" . print_r($this->tcaParameters, true) . "</pre>");
    }



    /**
     * Initializes tree repository builder
     */
    protected function initTreeRepositoryBuilder() {
        if ($this->nodeRepositoryClassName !== null && $this->nodeRepositoryClassName != '') {
            $treeRepositoryBuilder = Tx_PtExtbase_Tree_TreeRepositoryBuilder::getInstance();
            $treeRepositoryBuilder->setNodeRepositoryClassName($this->nodeRepositoryClassName);
        }
    }



    /**
     * Sets includes for CSS and JS
     */
    protected function addJsAndCssIncludes() {
        $doc = $this->getDocInstance();
        $pageRenderer = $doc->getPageRenderer();
        $compress = TRUE;

        // Load CSS
        $pageRenderer->addCssFile('fileadmin/ext-3.4.0/resources/css/ext-all.css', 'all', '', $compress);
    }

}
?>