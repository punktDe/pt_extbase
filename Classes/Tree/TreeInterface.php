<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

interface TreeInterface extends TraversableInterface
{
    /**
     * Returns node for a given uid
     *
     * @param integer $uid Uid of node
     * @return Node
     */
    public function getNodeByUid($uid);
    
    
    
    /**
     * Removes a node from the tree
     *
     * @param Node $node
     * @return TreeInterface
     */
    public function deleteNode(Node $node);



    /**
     * Moves a node given as first parameter into a node given as second parameter
     *
     * @param Node $nodeToBeMoved Node to be moved
     * @param Node $targetNode Node to move moved node into
     * @return TreeInterface
     */
    public function moveNode(Node $nodeToBeMoved, Node $targetNode);
    
    
    
    /**
     * Moves a node given as a first parameter in front of a node given as a second parameter 
     *
     * @param Node $nodeToBeMoved
     * @param Node $nodeToMoveBefore
     * @return TreeInterface
     */
    public function moveNodeBeforeNode(Node $nodeToBeMoved, Node $nodeToMoveBefore);
    
    
    
    /**
     * Moves a node given as first parameter after a node given as second parameter
     *
     * @param Node $nodeToBeMoved
     * @param Node $nodeToMoveAfter
     * @return TreeInterface
     */
    public function moveNodeAfterNode(Node $nodeToBeMoved, Node $nodeToMoveAfter);
    
    
    
    /**
     * Adds a given node into a given parent node
     *
     * @param Node $newNode Node to be added to tree
     * @param Node $parentNode Node to add new node into
     * @return TreeInterface
     */
    public function insertNode(Node $newNode, Node $parentNode);



    /**
     * Sets restricted depth of tree
     *
     * @param $restrictedDepth
     */
    public function setRestrictedDepth($restrictedDepth);



    /**
     * Sets respect restricted depth to given value.
     *
     * If set to true, tree builder will respect restricted depth, when building tree.
     *
     * @param bool $respectRestrictedDepth
     */
    public function setRespectRestrictedDepth($respectRestrictedDepth);
}
