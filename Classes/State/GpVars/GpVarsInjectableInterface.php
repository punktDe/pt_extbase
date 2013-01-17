<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
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
 * Interface for objects that can be injected by GP vars
 *
 * @package State
 * @subpackage GpVars
 * @author Michael Knoll 
 */
interface Tx_PtExtbase_State_GpVars_GpVarsInjectableInterface 
			extends Tx_PtExtbase_State_IdentifiableInterface {
				
    
	/**
	 * Injects GetPost Vars into object
	 *
	 * @param array $GPVars GP Var data to be injected into the object
	 */
	public function injectGPVars($GPVars);

}

?>