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

class Tx_PtExtbase_ViewHelpers_Widget_TreeViewHelper extends Tx_Fluid_Core_Widget_AbstractWidgetViewHelper {

	/**
	 * @var Tx_PtExtbase_ViewHelpers_Widget_Controller_TreeController
	 */
	protected $controller;


	/**
	 * Initialize arguments.
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('repository', 'string', 'Specifies the tree repository', false);
		$this->registerArgument('namespace', 'string', 'Specifies the tree namespace', false);
	}


	/**
	 * @param Tx_PtExtbase_ViewHelpers_Widget_Controller_TreeController $controller
	 * @return void
	 */
	public function injectController(Tx_PtExtbase_ViewHelpers_Widget_Controller_TreeController $controller) {
		$this->controller = $controller;
	}



	/**
	 * @return Tx_Extbase_MVC_Response
	 */
	public function render() {
		$this->saveTreeSettingsToSession();
		return  $this->initiateSubRequest();
	}


	/**
	 * @param $treeRepository
	 */
	protected function saveTreeSettingsToSession() {

		$treeSettings = array(
			'repository' => $this->arguments['repository'],
			'namespace' => $this->arguments['namespace'],
		);

		Tx_PtExtbase_State_Session_Storage_SessionAdapter::getInstance()->store('Tx_PtExtbase_Tree_Configuration', $treeSettings);
	}

}
