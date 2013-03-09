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
 * Class implements a factory for building a lifecycle manager 
 * 
 * @author Christoph Ehscheidt 
 * @author Michael Knoll
 * @package Domain
 * @subpackage Lifecycle
 */
class Tx_PtExtbase_Lifecycle_ManagerFactory {

	/**
	 * Holds the singleton instance of a lifecycle manager.
	 * 
	 * @var Tx_PtExtbase_Lifecycle_leManager
	 */
	protected static $instance = NULL;
	
	
	
	/**
	 * Factory method for lifecycle manager instances. Returns singleton instance 
	 * of lifecycle manager.
	 *
	 * @return Tx_PtExtbase_Lifecycle_Manager
	 */
	public static function getInstance() {
		if(self::$instance === NULL) {
			$lifecycleManager = new Tx_PtExtbase_Lifecycle_Manager();
			$lifecycleManager->updateState(Tx_PtExtbase_Lifecycle_Manager::START);
			self::$instance = $lifecycleManager;
		}
		
		return self::$instance;
	}
	
}

?>