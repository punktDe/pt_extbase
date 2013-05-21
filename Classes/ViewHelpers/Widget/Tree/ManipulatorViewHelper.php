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
class Tx_PtExtbase_ViewHelpers_Widget_Tree_ManipulatorViewHelper extends Tx_Fluid_Core_Widget_AbstractWidgetViewHelper {


	/**
	 * @var Tx_PtExtbase_ViewHelpers_Widget_Controller_TreeManipulatorController
	 */
	protected $controller;



	/**
	 * @param Tx_PtExtbase_ViewHelpers_Widget_Controller_TreeManipulatorController $manipulatorController
	 */
	public function injectManipulatorController(Tx_PtExtbase_ViewHelpers_Widget_Controller_TreeManipulatorController $manipulatorController) {
		$this->controller = $manipulatorController;
	}



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
	 * @return string
	 * @api
	 */
	public function render() {
		return $this->initiateSubRequest();
	}

}
?>