<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */


/**
 * Generic algorithm for traversing trees
 *
 * TreeWalker itself is doing nothing but traversing a tree in given order (depth-first or breadth-first).
 * You have to register one ore more visitors which are called whenever a node is visited for the first or last
 * time, all node-manipulation logic is implemented within those visitors.
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class TreeWalker
{
    /**
     * Holds a set of strategies that are invoked, whenever a node is visited
     *
     * @var array<TreeWalkerVisitorInterface>
     */
    protected $visitors;



    /**
     * If set to a value different to -1, we stop traversing tree, if we pass given depth
     *
     * @var integer
     */
    protected $restrictedDepth = -1;



    /**
     * @var TreeContext
     */
    protected $treeContext;


    /**
     * @param TreeContext $treeContext
     */
    public function injectTreeContext(TreeContext $treeContext)
    {
        $this->treeContext = $treeContext;
    }


    /**
     * Constructor for tree walker
     *
     * @param array $visitors
     * @throws \Exception
     */
    public function __construct($visitors)
    {
        foreach ($visitors as $visitor) {
            if (is_a($visitor, TreeWalkerVisitorInterface::class)) {
                $this->visitors[] = $visitor;
            } else {
                throw new \Exception('Given visitor does not implement TreeWalkerVisitorInterface. 1307902730');
            }
        }
    }
    
    
    
    /**
     * Traverses a tree depth-first search. Applying registered visitors whenever a node is visited.
     *
     * @param TraversableInterface $tree
     */
    public function traverseTreeDfs(TraversableInterface $tree)
    {
        $index = 1;

        // If we should respect depth-restriction for tree traversal, we set property
        if ($tree->getRespectRestrictedDepth()) {
            $this->restrictedDepth = $tree->getRestrictedDepth();
        }

        $level = 1;
        if ($this->restrictedDepth === -1 || $level <= $this->restrictedDepth) {
            $this->dfs($tree->getRoot(), $index, $level);
        }
    }
    
    
    
    /**
     * Helper method for doing a depth-first search on a node
     *
     * @param NodeInterface $node
     * @param integer &$index Referenced value of visitation index. Will be increased with every node visitation.
     * @param integer &$level Current level of visit in the tree starting at 1
     */
    protected function dfs(NodeInterface $node, &$index, &$level = 1)
    {
        if ($node->isAccessible() || $this->treeContext->isWritable()) {
            $this->doFirstVisit($node, $index, $level);
            $index = $index + 1;

            if ($node->getChildrenCount() > 0) {
                $level = $level + 1;
                if ($this->restrictedDepth === -1 || $level <= $this->restrictedDepth) {
                    foreach ($node->getChildren() as $child) {
                        /* @var $child NodeInterface */
                        $this->dfs($child, $index, $level);
                    }
                }
                $level = $level - 1;
            }

            $this->doLastVisit($node, $index, $level);
            $index = $index + 1;
        }
    }



    /**
     * Returns true, if given level is NOT deeper than restricted depth set in treewalker.
     *
     * @param integer $level Level to be compared with restricted depth
     * @return bool True, if level is not deeper than restricted depth
     */
    protected function levelIsBiggerThanRestrictedDepth($level)
    {
        error_log("level: " . $level . " restricted depth: " . $this->restrictedDepth);
        if ($this->restrictedDepth === -1) {
            return false;
        } elseif ($level > $this->restrictedDepth) {
            return true;
        } else {
            return false;
        }
    }
    
    

    /**
     * Calls registered visitors whenever a node is visited for the first time
     *
     * @param NodeInterface $node
     * @param $index
     */
    protected function doFirstVisit(NodeInterface $node, &$index, &$level)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->doFirstVisit($node, $index, $level);
        }
    }
    
    

    /**
     * Calls registered visitors whenever a node is visited for the last time
     *
     * @param NodeInterface $node
     * @param $index
     */
    protected function doLastVisit(NodeInterface $node, &$index, &$level)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->doLastVisit($node, $index, $level);
        }
    }


    /**
     * Traverses a tree breadth-first search. Applying registered visitors whenever a node is visited
     *
     * @param TraversableInterface $tree
     * @throws Exception
     */
    public function traverseTreeBfs(TraversableInterface $tree)
    {
        // TODO implement me!
        throw new Exception('Traversing tree BFS is not yet implemented!');
    }
}
