<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

interface TreeBuilderInterface
{
    /**
     * Returns an empty tree with root node labeled by given label
     *
     * @param string $namespace Namespace for tree
     * @param string $rootLabel Label for root node
     * @return Tree Empty tree object.
     */
    public function getEmptyTree($namespace, $rootLabel = '');
    
    
    
    /**
     * Builds a tree for given namespace.
     *
     * @param string $namespace Namespace to build tree for
     * @return Tree
     */
    public function buildTreeForNamespace($namespace);
}
