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
 * @author Daniel Lienert
 */

class Tx_PtExtbase_ViewHelpers_Tree_SelectorViewHelper extends Tx_Fluid_ViewHelpers_Form_TextfieldViewHelper {

	/**
	 * @var Tx_PtExtbase_Tree_TreeRepository
	 */
	protected $treeRepository;

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

		$this->registerArgument('repository', 'string', 'Specifies the tree repository', true);
		$this->registerArgument('namespace', 'string', 'Specifies the tree namespace', true);
		$this->registerArgument('multiple', 'boolean', 'Specifies if the tree is a multiple or single select tree', false, false);
	}



	/**
	 * Initialize the viewHelper
	 */
	public function initialize() {
		parent::initialize();

		$treeRepositoryBuilder = Tx_PtExtbase_Tree_TreeRepositoryBuilder::getInstance();
		$treeRepositoryBuilder->setNodeRepositoryClassName($this->arguments['repository']);

		$this->treeRepository = $treeRepositoryBuilder->buildTreeRepository();

		$this->multiple = $this->arguments['multiple'];
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
		$treeDiv = $this->getTreeDiv();
		$treeJS = $this->getTreeJS();

		$this->getTreeNodes();

		return $formField . $treeDiv . $treeJS;
	}



	protected function getTreeNodes() {
		$tree = $this->treeRepository->loadTreeByNamespace($this->arguments['namespace']);

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
	 * @return string
	 */
	protected function getTreeJS() {

		/** @var Tx_PtExtbase_ViewHelpers_Javascript_TemplateViewHelper $treeViewHelper  */
		$treeViewHelper = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager')->get('Tx_PtExtbase_ViewHelpers_Javascript_TemplateViewHelper');
		$treeViewHelper->setControllerContext($this->controllerContext);

		return $treeViewHelper->render('EXT:pt_extbase/Resources/Private/JSTemplates/Tree/SelectTree.js',
			array(
				'nodeJSON' => $this->getTreeNodes(),
				'multiple' => $this->multiple ? 'true': 'false',
			)
			,FALSE, FALSE
		);
	}


	/**
	 * @return string
	 */
	protected function getTreeDiv() {
		return '<div id="ptExtbaseTreeDiv"></div>';
	}
}
