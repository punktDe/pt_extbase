<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

interface TreeStorageInterface
{
    /**
     * Saves a tree to storage
     *
     * @param TreeInterface $tree
     */
    public function saveTree(TreeInterface $tree);
}
