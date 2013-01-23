<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
 *
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
 * Class implements a viewhelper that renders a tree selector widget.
 *
 * @example Usage:<code>
 *
 * <ptx:tree.selector repository="{nodeRepositoryClassName}"
 *				 namespace="{treeNamespace}"
 *				 name="{formFieldName}"
 *				 id="{formFieldName}"
 *				 value="{selectedValuesCommaSeparated}"
 *				 multiple="1"
 *               restrictedDepth="{restrictedDepth}"
 * />
 *
 * Following parameters are available:
 *
 * repository       Repository class name to be used as node repository (not as tree repository!)
 * namespace        Namespace for which to create tree
 * name             Name of the form field (see input.text viewhelper!)
 * value            Uid of selected node (if in 1:N mode) or comma separated list of UIDs (if in M:N mode)
 * multiple         If set to 1, multiple nodes can be selected in widget
 * restrictedDepth  If a value is given, tree is only rendered to given depth (1 = only root node is rendered)
 *
 * </code>
 *
 * @author Daniel Lienert
 */
class Tx_PtExtbase_ViewHelpers_Tree_SelectorViewHelper extends Tx_Fluid_ViewHelpers_Form_TextfieldViewHelper {

	/**
	 * @var string
	 */
	protected $nodes;



	/**
	 * @var boolean
	 */
	protected $multiple;



	/**
	 * Initialize arguments.
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->registerArgument('nodes', 'string', 'The tree nodes as JSON Array', false);
		$this->registerArgument('repository', 'string', 'Specifies the node repository', false);
		$this->registerArgument('namespace', 'string', 'Specifies the tree namespace', false);
		$this->registerArgument('multiple', 'boolean', 'Specifies if the tree is a multiple or single select tree', false, false);
		$this->overrideArgument('id', 'string', 'Specifies the field and div ID', true, 'ptExtbaseTreeSelector');
		$this->registerArgument('restrictedDepth', 'int', 'Depth of tree to be rendered', false);
		$this->registerArgument('expand', 'string', 'Expand Mode. "all" or "root"', false, 'root');
	}



	/**
	 * Initialize the viewHelper
	 */
	public function initialize() {
		parent::initialize();

		$this->multiple = $this->arguments['multiple'];
		$this->nodes = trim($this->arguments['nodes']);

		if(!$this->nodes) {
			if(!$this->arguments['repository']) throw new Exception('Either treeNodes or a treeNodeRepository has to be given to use the viewHelper.', 1328536673);
		}
	}



	/**
	 * Renders the treeSelector.
	 *
	 * @param boolean $required If the field is required or not
	 * @return string
	 * @api
	 */
	public function render($required = NULL) {
		$formField = parent::render($required, 'hidden', NULL);

		if(!$this->nodes) {
			$this->nodes = $this->getTreeNodes();
		}

		$treeDiv = $this->getTreeDiv();
		$treeJS = $this->getTreeJS($this->nodes);

		return $formField . $treeDiv . $treeJS;
	}



	/**
	 * Get Tree nodes as JSON array
	 *
	 * @return string JSON array
	 */
	protected function getTreeNodes() {

		$treeRepositoryBuilder = Tx_PtExtbase_Tree_TreeRepositoryBuilder::getInstance();
		$treeRepositoryBuilder->setNodeRepositoryClassName($this->arguments['repository']);

		$treeRepository = $treeRepositoryBuilder->buildTreeRepository();

		$tree = $treeRepository->loadTreeByNamespace($this->arguments['namespace']);

		if (isset($this->arguments['restrictedDepth'])) {
			$tree->setRestrictedDepth($this->arguments['restrictedDepth']);
			$tree->setRespectRestrictedDepth(TRUE);
		}

		$arrayWriterVisitor = new Tx_PtExtbase_Tree_ExtJsJsonWriterVisitor();
		$arrayWriterVisitor->setMultipleSelect($this->arguments['multiple']);
		$arrayWriterVisitor->setSelection($this->getSelection());

		$jsonTreeWriter = new Tx_PtExtbase_Tree_JsonTreeWriter(array($arrayWriterVisitor), $arrayWriterVisitor);

		return $jsonTreeWriter->writeTree($tree);
	}



	/**
	 * @return array|int
	 */
	protected function getSelection() {
		if($this->multiple) {
			return t3lib_div::trimExplode(',',$this->arguments['value'],TRUE);
		} else {
			return (int) trim($this->arguments['value']);
		}
	}



	/**
	 * Build and return the javascript via the javascript viewHelper
	 * @todo refactor JSViewHelper and move the marker code to a separate utility, call the utility here
	 *
	 * @param $treeNodes string treeNode JSON
	 * @return string
	 */
	protected function getTreeJS($treeNodes) {

		/** @var Tx_PtExtbase_ViewHelpers_Javascript_TemplateViewHelper $treeViewHelper  */
		$treeViewHelper = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager')->get('Tx_PtExtbase_ViewHelpers_Javascript_TemplateViewHelper');
		//$treeViewHelper->setControllerContext($this->controllerContext);

		$treeViewHelper->initialize();

		return $treeViewHelper->render('EXT:pt_extbase/Resources/Private/JSTemplates/Tree/SelectTree.js',
			array(
				'nodeJSON' => $treeNodes,
				'multiple' => $this->multiple ? 'true': 'false',
				'fieldId' => $this->arguments['id'],
				'expand' => $this->arguments['expand'],
			)
			,FALSE, FALSE
		);
	}



	/**
	 * @return string
	 */
	protected function getTreeDiv() {
		return '<div id="'.$this->arguments['id'].'Div"></div>';
	}

}
?>