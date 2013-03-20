<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
*  All rights reserved
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
 * Class implements a tree walker for nested sets
 *
 * Nested sets tree walker traverses a tree DFS and returns
 * nodes with left and right numbering for Nested Sets storage.
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tree_NestedSetTreeWalker extends Tx_PtExtbase_Tree_TreeWalker {

    /**
     * Holds instance of nested sets visitor.
     *
     * Although we have this visitor in array of visitors for this tree walker,
     * we have a special reference here to get further information after
     * tree traversal!
     *
     * @var Tx_PtExtbase_Tree_NestedSetVisitor
     */
    protected $nestedSetVisitor;



    /**
     * Returns instance of Nested Sets Tree Walker
     *
     * @static
     * @return Tx_PtExtbase_Tree_NestedSetTreeWalker
     */
    public static function getInstance() {
        $nestedSetTreeWalkerVisitor = new Tx_PtExtbase_Tree_NestedSetVisitor();
        $nestedSetTreeWalker = new Tx_PtExtbase_Tree_NestedSetTreeWalker(array($nestedSetTreeWalkerVisitor), $nestedSetTreeWalkerVisitor);
        return $nestedSetTreeWalker;
    }



    /**
     * Constructor for nested sets tree walker.
     *
     * We add nestedSetVisitor explicitly as reference. You have to add it to array of visitors, too!
     *
     * @param array $visitors
     * @param Tx_PtExtbase_Tree_NestedSetVisitor $nestedSetVisitor
     */
    public function __construct(array $visitors, Tx_PtExtbase_Tree_NestedSetVisitor $nestedSetVisitor) {
        parent::__construct($visitors);
        $this->nestedSetVisitor = $nestedSetVisitor;
    }



    /**
     * Returns nodes found during depth-first-search traversal of tree with nested sets numbering.
     *
     * @param Tx_PtExtbase_Tree_NestedSetTreeInterface $tree
     * @return array<Tx_PtExtbase_Tree_NestedSetNodeInterface>
     */
    public function traverseTreeAndGetNodes(Tx_PtExtbase_Tree_NestedSetTreeInterface $tree) {
        // TODO we should be able to pass tree here - not root of tree...
        $this->traverseTreeDfs($tree);
        $nodes = $this->nestedSetVisitor->getVisitedNodes();
        return $nodes;
    }
	
}
?>