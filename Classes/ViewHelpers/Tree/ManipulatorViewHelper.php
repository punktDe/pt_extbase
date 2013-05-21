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
 * Class implements a widget viewhelper for rendering trees that can be manipulated using ajax requests
 *
 * @author Daniel Lienert
 */
class Tx_PtExtbase_ViewHelpers_Tree_ManipulatorViewHelper extends Tx_Fluid_ViewHelpers_Form_TextfieldViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('repository', 'string', 'Specifies the tree repository', false);
		$this->registerArgument('namespace', 'string', 'Specifies the tree namespace', false);
		$this->registerArgument('type', 'string', 'Specifies the tree type', false);
		$this->registerArgument('respectEnableFields', 'boolean', 'Should the tree respect enable fields', false);
	}



	/**
	 * Renders the treeSelector.
	 *
	 * @param boolean $required If the field is required or not
	 * @return string
	 * @api
	 */
	public function render($required = NULL) {
		$formField = parent::render($required, 'text', NULL);
		$treeDiv = $this->getTreeDiv();
		$treeJS = $this->getTreeJS();

		return $formField . $treeDiv . $treeJS;
	}



	protected function getTreeJS() {

		/** @var Tx_PtExtbase_ViewHelpers_Javascript_TemplateViewHelper $treeViewHelper  */
		$treeViewHelper = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager')->get('Tx_PtExtbase_ViewHelpers_Javascript_TemplateViewHelper');
		//$treeViewHelper->setControllerContext($this->controllerContext);

		//return $treeViewHelper->render('EXT:pt_extbase/Resources/Private/JSTemplates/Tree/SelectTree.js',
		return $treeViewHelper->render('EXT:pt_extbase/Resources/Private/JSTemplates/Tree/ManipulationTree.js',
			array(
				'baseUrl' => $this->getBaseURL(),
				'dbNodeTable' => 'tx_ptcertification_domain_model_category'
			), FALSE, FALSE
		);
	}



	/**
	 * @return string
	 */
	protected function getTreeDiv() {
		return '<div id="ptExtbaseTreeDiv"></div>';
	}



	/**
	 * Save settings to user session
	 */
	protected function saveTreeSettingsToSession() {

		$treeSettings = array(
			'repository' => $this->arguments['repository'],
			'namespace' => $this->arguments['namespace'],
			'respectEnableFields' => $this->arguments['respectEnableFields'],
		);

		Tx_PtExtbase_State_Session_Storage_SessionAdapter::getInstance()->store('Tx_PtExtbase_Tree_Configuration', $treeSettings);
	}



	/**
	 * Determine the baseURl by context
	 * @return string
	 */
	protected function getBaseURL() {
		if (TYPO3_MODE == 'BE') {
			$baseUrl = 'ajax.php?ajaxID=ptxAjax';
		} elseif (TYPO3_MODE == 'FE') {
			$baseUrl = 'index.php?eID=ptxAjax';
		}

		return $baseUrl;
	}

}
?>