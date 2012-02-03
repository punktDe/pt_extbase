<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2012 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
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
 * Controller handles tree widget
 *
 * We only use this for templating issues. No tree-manipulation is implemented here!
 *
 * @author Daniel Lienert
 * @author Michael Knoll
 */
class Tx_PtExtbase_ViewHelpers_Widget_Controller_TreeController extends Tx_Fluid_Core_Widget_AbstractWidgetController {

	/**
	 * Renders index action of tree widget
     *
     * @return string Rendered widget
	 */
	public function indexAction() {

        // We have to set base base URL in FE and BE
        if (TYPO3_MODE == 'BE') {
            $baseUrl = 'ajax.php?ajaxID=ptxAjax';
        } elseif (TYPO3_MODE == 'FE') {
            $baseUrl = 'index.php?eID=ptxAjax';
        }

        $this->view->assign('baseUrl', $baseUrl);

	}

}
?>