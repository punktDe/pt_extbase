<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

interface TraversableInterface
{
    /**
     * Returns root node of traversable object
     * 
     * @return NodeInterface
     */
    public function getRoot();



    /**
     * Returns true, if restricted depth of tree should be respected
     *
     * @return bool True, if restricted depth should be respected
     */
    public function getRespectRestrictedDepth();



    /**
     * Getter for restricted depth
     *
     * @return integer Restricted depth
     */
    public function getRestrictedDepth();
}
