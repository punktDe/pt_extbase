<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

interface NestedSetTreeInterface extends TreeInterface
{
    /**
     * Returns deleted nodes of a tree
     *
     * @return array<NodeInterface>
     */
    public function getDeletedNodes();



    /**
     * Returns namespace of tree
     *
     * @return string namespace
     */
    public function getNamespace();



    /**
     * Sets namespace of tree
     *
     * @param string $namespace
     */
    public function setNamespace($namespace);
}
