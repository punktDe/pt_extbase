<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class ArrayWriterVisitor implements TreeWalkerVisitorInterface
{
    /**
     * Holds an array representing array structure of nodes
     *
     * How should array structure look like at the end:
     *
     * array (
     *      uid         => 1,
     *      label       => 'root',
     *      children    => array (
     *          1       => array (
     *              uid         => 2,
     *              label       => 'first child',
     *              children    => array(...)
     *          ),
     *          2       => array (...),
     *          ...
     *      )
     * )
     *
     * @var array
     */
    protected $nodeArray = [];



    /**
     * Holds stack of unfinished nodes
     *
     * @var Stack
     */
    protected $nodeStack;



    /**
     * Constructor for visitore
     */
    public function __construct()
    {
        $this->nodeStack = new Stack();
    }



    /**
     * @see TreeWalkerVisitorInterface::doFirstVisit()
     *
     * @param NodeInterface $node
     * @param integer &$index Holds the visitation index of treewalker
     * @param integer &$level Holds level of visitation in tree, starting at 1
     */
    public function doFirstVisit(NodeInterface $node, &$index, &$level)
    {
        $arrayForNode = [
              'uid' => $node->getUid(),
            'label' => $node->getLabel(),
            'children' => [],
            'disabled' => !$node->isAccessible(),
        ];

        $this->nodeStack->push($arrayForNode);
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
        $currentNode = $this->nodeStack->top();
        $this->nodeStack->pop();
        if (!$this->nodeStack->isEmpty()) {
            $parentNode = $this->nodeStack->top();
            $this->nodeStack->pop();
            $parentNode['children'][] = $currentNode;
            $this->nodeStack->push($parentNode);
        } else {
            $this->nodeArray = $currentNode;
        }
    }



    /**
     * Returns array structure for visited nodes
     *
     * @return array
     */
    public function getNodeArray()
    {
        return $this->nodeArray;
    }
}
