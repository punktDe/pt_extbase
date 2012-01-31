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
 * Writer outputs array for a tree
 *
 * TODO refactor this class. Actually it does not extend treewalker but rather uses it for traversal. This makes API clearer.
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tree_ArrayTreeWriter extends Tx_PtExtbase_Tree_TreeWalker {

    /**
     * Holds an instance of array writer visitor
     *
     * @var Tx_PtExtbase_Tree_ArrayWriterVisitor
     */
    protected $arrayWriterVisitor;



    /**
     * Creates a new instance of array writer
     *
     * @param array $visitors
     * @return Tx_PtExtbase_Tree_ArrayTreeWriter
     */
    public static function getInstance(array $visitors = array()) {
        $arrayWriterVisitor = new Tx_PtExtbase_Tree_ArrayWriterVisitor();
        $visitors[] = $arrayWriterVisitor;
        $arrayTreeWriter = new Tx_PtExtbase_Tree_ArrayTreeWriter($visitors, $arrayWriterVisitor);
        return $arrayTreeWriter;
    }



    /**
     * Constructor for array tree writer
     *
     * @param array $visitors
     * @param Tx_PtExtbase_Tree_ArrayWriterVisitor $arrayWriterVisitor
     */
    public function __construct(array $visitors, Tx_PtExtbase_Tree_TreeWalkerVisitorInterface $arrayWriterVisitor) {
        parent::__construct($visitors);
        $this->arrayWriterVisitor = $arrayWriterVisitor;
    }



    /**
     * Returns array of given tree
     *
     * @param Tx_PtExtbase_Tree_TreeInterface $tree
     * @return array
     */
    public function writeTree(Tx_PtExtbase_Tree_TreeInterface $tree) {
        $this->traverseTreeDfs($tree);
        $nodeArray = $this->arrayWriterVisitor->getNodeArray();
        return $nodeArray;
    }

}
?>