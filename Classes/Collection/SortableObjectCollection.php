<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
*  Authors: Daniel Lienert, Michael Knoll, Joachim Mathes
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
 * Abstract object collection class. Object collection can be sorted if objects
 * implement the sortable interface.
 *
 * @author Joachim Mathes
 * @package pt_extbase
 * @subpackaga Collection
 */
abstract class Tx_PtExtbase_Collection_SortableObjectCollection extends Tx_PtExtbase_Collection_ObjectCollection {

	/**
	 * Sort items in decreasing order with respect to sorting value
	 *
	 * @return void
	 */
	public function sort() {
		usort($this->itemsArr, array('self', 'compareItems'));
	}

	/**
	 * Compare two objects which implement SortableItemInterface
	 *
	 * Callable method for usort()
	 *
	 * @param Tx_PtExtbase_Collection_SortableEntityInterface $a
	 * @param Tx_PtExtbase_Collection_SortableEntityInterface $b
	 * @return int -1, 0 or 1
	 */
	public static function compareItems(Tx_PtExtbase_Collection_SortableEntityInterface $a, Tx_PtExtbase_Collection_SortableEntityInterface $b) {
		$sortingValueA = $a->getSortingValue();
		$sortingValueB = $b->getSortingValue();
		if ($sortingValueA > $sortingValueB) return 1;
		if ($sortingValueA == $sortingValueB) return 0;
		if ($sortingValueA < $sortingValueB) return -1;
	}

}
?>