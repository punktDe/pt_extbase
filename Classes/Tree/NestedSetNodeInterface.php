<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */


interface NestedSetNodeInterface extends NodeInterface
{
    /**
     * Getter for root node uid
     *
     * @return integer
     */
    public function getRoot();


    /**
     * Setter for root node uid
     *
     * @param integer $root
     */
    public function setRoot($root);


    /**
     * @abstract
     *
     * @return string Label
     */
    public function getLabel();


    /**
     * @abstract
     * @param string $label
     */
    public function setLabel($label);


    /**
     * Getter for nested sets right number in tree
     *
     * @return integer
     */
    public function getRgt();


    /**
     * Setter for nested sets right number in tree
     *
     * @param integer $rgt
     */
    public function setRgt($rgt);


    /**
     * Getter for nested sets left number tree
     *
     * @return integer
     */
    public function getLft();


    /**
     * Setter for nested sets right number in tree
     *
     * @param integer $lft
     */
    public function setLft($lft);
}
