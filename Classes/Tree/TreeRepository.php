<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class TreeRepository
{
    /**
     * Holds instance of node repository
     *
     * @var NodeRepositoryInterface
     */
    protected $nodeRepository;



    /**
     * Holds instance of tree builder
     *
     * @var TreeBuilder
     */
    protected $treeBuilder;



    /**
     * Holds instance of tree storage
     *
     * @var TreeStorageInterface
     */
    protected $treeStorage;



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
     * Constructor for tree repository
     *
     * @param NodeRepositoryInterface $nodeRepository
     * @param TreeBuilder $treeBuilder
     * @param TreeStorageInterface $treeStorage
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, TreeBuilder $treeBuilder, TreeStorageInterface $treeStorage)
    {
        $this->nodeRepository = $nodeRepository;
        $this->treeBuilder = $treeBuilder;
        $this->treeStorage = $treeStorage;
    }


    /**
     * Loads tree for a given namespace
     *
     * @param string $namespace Namespace to build tree for
     * @return Tree Tree build for given namespace
     */
    public function loadTreeByNamespace($namespace)
    {
        if ($this->treeContext->respectEnableFields()) {
            return $this->treeBuilder->buildTreeForNamespaceWithoutInaccessibleSubtrees($namespace);
        } else {
            return $this->treeBuilder->buildTreeForNamespace($namespace);
        }
    }



    /**
     * Updates given tree
     *
     * @param Tree $tree Tree to be updated
     */
    public function update($tree)
    {
        $this->treeStorage->saveTree($tree);
    }



    /**
     * Returns an empty tree for given namespace and root label
     *
     * @param $namespace
     * @param string $rootLabel
     * @return Tree Empty tree for given namespace and root label
     */
    public function getEmptyTree($namespace, $rootLabel = 'root')
    {
        return $this->treeBuilder->getEmptyTree($namespace, $rootLabel);
    }



    /**
     * Setter for respectRestrictedDepth.
     *
     * If set to true, respect restricted depth is set to true in trees returned by this repository
     *
     * @param bool $respectRestrictedDepth
     */
    public function setRespectRestrictedDepth($respectRestrictedDepth = true)
    {
        $this->treeBuilder->setRespectRestrictedDepth($respectRestrictedDepth);
    }
}
