<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class NestedSetTreeWalker extends TreeWalker
{
    /**
     * Holds instance of nested sets visitor.
     *
     * Although we have this visitor in array of visitors for this tree walker,
     * we have a special reference here to get further information after
     * tree traversal!
     *
     * @var NestedSetVisitor
     */
    protected $nestedSetVisitor;


    /**
     * Returns instance of Nested Sets Tree Walker
     *
     * @static
     * @return NestedSetTreeWalker
     */
    public static function getInstance()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $nestedSetTreeWalkerVisitor = $objectManager->get(NestedSetVisitor::class);
        $nestedSetTreeWalker = $objectManager->get(NestedSetTreeWalker::class, [$nestedSetTreeWalkerVisitor], $nestedSetTreeWalkerVisitor);
        return $nestedSetTreeWalker;
    }


    /**
     * Constructor for nested sets tree walker.
     *
     * We add nestedSetVisitor explicitly as reference. You have to add it to array of visitors, too!
     *
     * @param array $visitors
     * @param NestedSetVisitor $nestedSetVisitor
     * @throws \Exception
     */
    public function __construct(array $visitors, NestedSetVisitor $nestedSetVisitor)
    {
        parent::__construct($visitors);
        $this->nestedSetVisitor = $nestedSetVisitor;
    }


    /**
     * Returns nodes found during depth-first-search traversal of tree with nested sets numbering.
     *
     * @param NestedSetTreeInterface $tree
     * @return array<NestedSetNodeInterface>
     */
    public function traverseTreeAndGetNodes(NestedSetTreeInterface $tree)
    {
        // TODO we should be able to pass tree here - not root of tree...
        $this->traverseTreeDfs($tree);
        $nodes = $this->nestedSetVisitor->getVisitedNodes();
        return $nodes;
    }
}
