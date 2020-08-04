<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

interface NodeRepositoryInterface
{
    /**
     * Returns nodes for a given namespace
     * 
     * Nodes are ordered by left-value 
     *
     * @param string $namespace
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<NodeInterface>
     */
    public function findByNamespace($namespace);



    /**
     * Updates a given node if it has already been added to repository or adds it.
     *
     * @abstract
     * @param NodeInterface $node
     */
    public function updateOrAdd(NodeInterface $node);
}
