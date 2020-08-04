<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class NestedSetTreeStorage implements TreeStorageInterface
{
    /**
     * Holds an instance of node repository
     *
     * @var NodeRepository
     */
    protected $nodeRepository;



    /**
     * Holds instance of nested sets tree walker
     *
     * @var NestedSetTreeWalker
     */
    protected $nestedSetTreeWalker;
    
    
    
    /**
     * Constructor for nested set tree storage
     *
     * @param NodeRepository $nodeRepository Node repository to store nodes in
     */
    public function __construct(NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
        // TODO put this into creation method
        $this->nestedSetTreeWalker = NestedSetTreeWalker::getInstance();
    }


    /**
     * Saves a tree to storage
     *
     * @param TreeInterface $tree
     * @throws \Exception
     */
    public function saveTree(TreeInterface $tree)
    {
        if (!is_a($tree, NestedSetTreeInterface::class)) {
            throw new \Exception('NestedSetTreeStorage can only persist trees that implement NestedSetTreeInterface! 1327695444');
        }

        $this->removeDeletedNodesOfGivenTree($tree);

        $nodes = $this->nestedSetTreeWalker->traverseTreeAndGetNodes($tree);

        foreach ($nodes as $node) { /* @var $node NodeInterface */
            $this->setTreeNamespaceOnNode($tree, $node);
            $this->nodeRepository->updateOrAdd($node);
        }

        $this->setTreeNamespaceOnNode($tree, $tree->getRoot());
        $this->nodeRepository->updateOrAdd($tree->getRoot());
    }



    /**
     * Removes deleted nodes of a given tree from node repository
     *
     * @param NestedSetTreeInterface $tree Tree whose deleted nodes should be removed from repository
     */
    protected function removeDeletedNodesOfGivenTree(NestedSetTreeInterface $tree)
    {
        foreach ($tree->getDeletedNodes() as $deletedNode) {
            $this->nodeRepository->remove($deletedNode);
        }
    }



    /**
     * Sets namespace of given tree on given node
     *
     * @param NestedSetTreeInterface $tree
     * @param NodeInterface $node
     * @throws \Exception
     */
    protected function setTreeNamespaceOnNode(NestedSetTreeInterface $tree, NodeInterface $node)
    {
        $namespace = $tree->getNamespace();
        if ($namespace !== null && $namespace !== '') {
            $node->setNamespace($namespace);
        } else {
            throw new \Exception('Trying to store a node of a tree that has no namespace set! Namespace is required on a tree to be stored! 1327756309');
        }
    }
}
