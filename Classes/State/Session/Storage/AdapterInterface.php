<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2011 Rainer Kuhn, Wolfgang Zenker, 
*                Fabrizio Branca, Michael Knoll
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
 * Storage adapter interface for reading and writing data to session
 *
 * @author      Rainer Kuhn
 * @author      Michael Knoll
 * @package     State
 * @subpackage  Session
 */
interface Tx_PtExtbase_State_Session_Storage_AdapterInterface {

	/**
	 * Returns the value of a storage key
	 *
	 * @param mixed $key Storage key
	 */
	public function read($key);



	/**
	 * Stores a value into a storage key
	 *
	 * @param mixed $key Storage key
	 * @param mixed $value Storage value
	 */
	public function store($key, $value);



	/**
	 * Deletes/unsets a storage key
	 *
	 * @param mixed $key Storage key to delete/unset
	 */
	public function delete($key);

}

?>