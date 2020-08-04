<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class NestedSetVisitor implements TreeWalkerVisitorInterface
{
    /**
     * Holds an array of nodes that has already been visited
     *
     * @var array<NestedSetNodeInterface>
     */
    protected $visitedNodes = [];



    /**
     * @see TreeWalkerVisitorInterface::doFirstVisit()
     *
     * @param NodeInterface $node
     * @param integer &$index Holds the visitation index of treewalker
     * @param integer &$level Holds level of visitation in tree, starting at 1
     */
    public function doFirstVisit(NodeInterface $node, &$index, &$level)
    {
        $node->setLft($index);
    }


    
    /**
     * @see TreeWalkerVisitorInterface::doLastVisit()
     *
     * @param NodeInterface $node
     * @param integer &$index Holds the visitation index of treewalker
     * @param integer &$level Holds level of visitation in tree, starting at 1
     */
    public function doLastVisit(NodeInterface $node, &$index, &$level)
    {
        $node->setRgt($index);
        $this->visitedNodes[] = $node;
    }



    /**
     * Returns array of visited nodes
     *
     * @return array<NestedSetNodeInterface>
     */
    public function getVisitedNodes()
    {
        return $this->visitedNodes;
    }
}
