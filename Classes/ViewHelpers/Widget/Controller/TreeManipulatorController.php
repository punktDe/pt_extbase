<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2012 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
*  Authors: Daniel Lienert, Sebastian Helzle
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
* Class implements actions for tree manipulation with ajax calls
*
* @author Daniel Lienert
* @author Michael Knoll
* @author Sebastian Helzle
*/
class Tx_PtExtbase_ViewHelpers_Widget_Controller_TreeManipulatorController extends Tx_Fluid_Core_Widget_AbstractWidgetController {


	protected function initializeView(Tx_Extbase_MVC_View_ViewInterface $view) {
		parent::initializeView($view);

		$view->assign('baseUrl', $this->getBaseURL());
	}


	public function indexAction() {

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