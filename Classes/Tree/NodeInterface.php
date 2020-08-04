<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;

interface NodeInterface extends DomainObjectInterface
{
    /*********************************************************************************************************
     * Getters and setters for advanced domain logic. NOT USED FOR PERSISTENCE!
     *********************************************************************************************************/

    /**
     * Setter for parent node
     *
     * @param NodeInterface $node
     */
    public function setParent(NodeInterface $node);


    /**
     * Getter for parent node
     *
     * @return NodeInterface
     */
    public function getParent();


    /**
     * Getter for child nodes
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getChildren();


    /**
     * Get count of children recursively
     *
     * TODO is this really necessary for this interface?
     *
     * @return integer
     */
    public function getChildrenCount();


    /**
     * Returns level of node (0 if node is root).
     *
     * Level is equal to depth
     * of node in tree where root has depth 0.
     *
     * @return integer
     */
    public function getLevel();


    /**
     * Indicates if this node is accessible by the user
     * and should therefore be visited by a visitor (and rendered)
     *
     * @abstract
     *
     * @return boolean
     */
    public function isAccessible();


    /**
     * Returns sub-nodes in a flat list. The result is ordered
     * in such a way that it reflects the structure of the tree (dfs):
     *
     * cat 1
     * - cat 1.1
     * -- cat 1.1.1
     * -- cat 1.1.2
     * - cat 1.2
     * -- cat 1.2.1
     * -- cat 1.2.2
     *
     * Will return
     *
     * cat 1
     * cat 1.1
     * cat 1.1.1
     * cat 1.1.2
     * cat 1.2
     * cat 1.2.1
     * cat 1.2.2
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getSubNodes();


    /*********************************************************************************************************
     * Domain logic
     *********************************************************************************************************/

    /**
     * Adds a child node to children at end of children
     *
     * @param NodeInterface $node
     */
    public function addChild(NodeInterface $node);


    /**
     * Adds a new child node after a given child node
     *
     * @param NodeInterface $newChildNode
     * @param NodeInterface $nodeToAddAfter
     */
    public function addChildAfter(NodeInterface $newChildNode, NodeInterface $nodeToAddAfter);


    /**
     * Adds a new child node before a given child node
     *
     * @param NodeInterface $newChildNode
     * @param NodeInterface $nodeToAddBefore
     * @param bool $updateLeftRight
     */
    public function addChildBefore(NodeInterface $newChildNode, NodeInterface $nodeToAddBefore);


    /**
     * Removes given child node
     *
     * @param NodeInterface $node
     * @param bool $updateLeftRight
     */
    public function removeChild(NodeInterface $node);


    /**
     * Returns true, if node has children
     *
     * @return bool
     */
    public function hasChildren();


    /**
     * Returns true, if node has a parent
     *
     * @return bool True, if node has parent node
     */
    public function hasParent();


    /**
     * Returns true, if node is root
     *
     * @return boolean True, if node is root
     */
    public function isRoot();


    /**
     * Sets namespace of node
     *
     * @param $namespace
     */
    public function setNamespace($namespace);


    /**
     * Returns namespace of node
     *
     * @return string Namespace of node
     */
    public function getNamespace();
}
