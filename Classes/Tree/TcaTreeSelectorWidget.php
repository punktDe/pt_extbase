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
 * Use the following lines of code for inserting tree widget in your TCA configuration
 *
 * $tempColumns = Array (
 *     'tx_ptextbasetests_domain_model_categorytest_categoryuid' => Array (
 *         'exclude' => 1,
 *         'label' => 'Category',
 *         'config' => Array (
 *             'type' => 'select',
 *             'form_type' => 'user',
 *             'userFunc' => 'EXT:pt_extbase/Classes/Tree/TcaTreeSelectorWidget.php:Tx_PtExtbase_Tree_TcaTreeSelectorWidget->renderTcaTreeSelectorWidget',
 *             'foreign_table' => 'tx_ptextbase_tree_node',
 *             'minitems' => 0,
 *             'maxitems' => 500,
 *             'MM' => 'tx_ptextbasetests_categorytest_mm',
 *             'parameters' => array(
 *                'extensionName' => 'Pt_Extbase',                                                     // ATM this is not really required. It would be required, if we use TCA template in a more advanced way and want to render links with FLUID
 *                 'pluginName' => 'web_PtExtbaseTxPtExtbaseM1',                                        // ATM this is not really required. It would be required, if we use TCA template in a more advanced way and want to render links with FLUID
 *                 'treeNamespace' => 'tx_ptcertification_domain_model_category',                       // Set tree namespace to the name you want to store your nodes with. ATM this can only be an existing namespace. We have to find a way to create trees initially in the backend.
 *                 //'templatePath' => 'EXT:pt_extbase/Resources/Private/Templates/Tca/Tree.html',        // This is the path to the template we use for rendering the tree. Should be made default setting and be overwritable here. ATM it does not work if you don't set it
 *                 //'nodeRepositoryClassName' => 'Tx_PtExtbase_Tree_NodeRepository',                     // Class name of repository that should be used for node storage (if left empty, Tx_PtExtbase_Tree_NodeRepository is taken)
 *                //'restrictedDepth' => 3                                                               // Determines how many levels of the tree should be rendered. 1 = only root node is rendered, 2 = root node and its children are rendered, ...
 *                //'expand' => 'root"                                                               // Expand the tree, posible are "all", "root" or none
 *             )
 *         )
 *     )
 * );
 *
 * // Add field to categoryTest TCA
 * t3lib_div::loadTCA("tx_ptextbasetests_domain_model_categorytest");
 * t3lib_extMgm::addTCAcolumns("tx_ptextbasetests_domain_model_categorytest",$tempColumns,1);
 * t3lib_extMgm::addToAllTCAtypes("tx_ptextbasetests_domain_model_categorytest","tx_ptextbasetests_domain_model_categorytest_categoryuid,TEST02;;;;1-1-1", '', 'after:name');
 *
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
    protected $templatePath = 'EXT:pt_extbase/Resources/Private/Templates/Tca/Tree.html';



    /**
     * Holds class name for node repository
     *
     * @var string
     */
    protected $nodeRepositoryClassName = 'Tx_PtExtbase_Tree_NodeRepository';



    /**
     * Holds depth of tree
     *
     * -1 means all levels of the tree are rendered.
     *
     * @var int
     */
    protected $restrictedDepth = -1;


	/**
	 * How to expand the tree: all, root, none
	 * @var string
	 */
	protected $expand = 'root';


    /**
     * User function to render TCA selector
     *
     * @param array $parameters
     * @param null $fObj
     */
    public function renderTcaTreeSelectorWidget(array $parameters=array(), $fObj=null) {
        // Backend form should be rendered no matter what happens here, so we catch exception
        try {
            $this->init($parameters, $fObj);
            $this->addJsAndCssIncludes();

            return $this->fluidRenderer->render();
        } catch (Exception $e) {
            return $e->getMessage();
        }
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

        if (array_key_exists('restrictedDepth', $fieldConfigParameters) && $fieldConfigParameters['restrictedDepth'] !== '') {
            $this->restrictedDepth = $fieldConfigParameters['restrictedDepth'];
        }

		if (array_key_exists('expand', $fieldConfigParameters) && $fieldConfigParameters['expand'] !== '') {
			$this->expand = $fieldConfigParameters['expand'];
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
        $this->fluidRenderer->assign('isManyToManyField', $this->isManyToManyField);
        $this->fluidRenderer->assign('is1ToManyField', $this->is1ToManyField);
        $this->fluidRenderer->assign('multiple', $this->isManyToManyField);
        $this->fluidRenderer->assign('restrictedDepth', $this->restrictedDepth);
        $this->fluidRenderer->assign('expand', $this->expand);
    }



    /**
     * Initializes tree repository builder
     */
    protected function initTreeRepositoryBuilder() {
        if ($this->nodeRepositoryClassName !== null && $this->nodeRepositoryClassName != '') {
            $treeRepositoryBuilder = Tx_PtExtbase_Tree_TreeRepositoryBuilder::getInstance();
            $treeRepositoryBuilder->setNodeRepositoryClassName($this->nodeRepositoryClassName);
            $treeRepositoryBuilder->setRestrictedDepth($this->restrictedDepth);
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