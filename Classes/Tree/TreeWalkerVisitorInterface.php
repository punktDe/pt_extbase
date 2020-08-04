<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

interface TreeWalkerVisitorInterface
{
    /**
     * Run whenever a node is visited for the first time
     *
     * @param NodeInterface $node
     * @param integer &$index Holds the visitation index of treewalker
     * @param integer &$level Holds level of visitation in tree, starting at 1
     */
    public function doFirstVisit(NodeInterface $node, &$index, &$level);
    
    
    
    /**
     * Run whenever a node is visited for the last time 
     *
     * @param NodeInterface $node
     * @param integer &$index Holds the visitation index of treewalker
     * @param integer &$level Holds level of visitation in tree, starting at 1
     */
    public function doLastVisit(NodeInterface $node, &$index, &$level);
}
