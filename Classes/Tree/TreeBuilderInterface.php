<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Michael Knoll <mimi@kaktusteam.de>
*  			Daniel Lienert <daniel@lienert.cc>
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
 * Interface for classes implementing a tree builder
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
interface Tx_PtExtbase_Tree_TreeBuilderInterface {

    /**
     * Returns an empty tree with root node labeled by given label
     *
     * @param string $namespace Namespace for tree
     * @param string $rootLabel Label for root node
     * @return Tx_PtExtbase_Tree_Tree Empty tree object.
     */
    public function getEmptyTree($namespace, $rootLabel = '');
	
	
	
	/**
	 * Builds a tree for given namespace.
	 *
	 * @param string $node Namespace to build tree for
	 * @return Tx_PtExtbase_Tree_Tree
	 */
	public function buildTreeForNamespace($namespace);



    /**
     * Setter for restricted depth.
     *
     * If depth is restricted, tree is build only to given level by tree builder.
     *
     * @abstract
     * @param int $restrictedDepth
     */
    public function setRestrictedDepth($restrictedDepth);



    /**
     * Sets respect restricted depth to given value.
     *
     * If set to true, tree builder will respect restricted depth, when building tree.
     *
     * @abstract
     * @param bool $respectRestrictedDepth
     */
    public function setRespectRestrictedDepth($respectRestrictedDepth);
	
}
?>