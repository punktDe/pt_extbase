<?php
namespace PunktDe\PtExtbase\Collection;

/***************************************************************
*  Copyright notice
*
*  (c) 2005-2011 Joachim Mathes
*
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
 * Sortable entity interface
 *
 * @author Joachim Mathes
 * @package pt_extbase
 * @subpackaga Collection
 */
interface SortableEntityInterface
{
    /**
     * @return integer Sorting value
     */
    public function getSortingValue();
}
