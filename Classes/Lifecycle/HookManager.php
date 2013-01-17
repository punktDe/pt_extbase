<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
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
 * Class for hooking into TYPO3 page-request lifecycle
 * 
 * @package Lifecycle
 * @author Christoph Ehscheidt 
 */
class tx_PtExtbase_Lifecycle_HookManager {
	
	/**
	 * Sends END signal to lifecycle manager, when TYPO3 is going to shut down
	 *
	 * @param array $params
	 * @param unknown_type $reference
	 */
	public function updateEnd(&$params, &$reference) {
		
		//If the class can not be resolved, we are not in an lifecycle-managed context. therefore exit here.
		if(!class_exists('Tx_PtExtbase_Lifecycle_ManagerFactory')) return;
		
		$lifecycle = Tx_PtExtbase_Lifecycle_ManagerFactory::getInstance();
		$lifecycle->updateState(Tx_PtExtbase_Lifecycle_Manager::END);
		
	}
	
}

?>