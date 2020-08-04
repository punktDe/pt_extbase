<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ArrayTreeWriter extends TreeWalker
{
    /**
     * Holds an instance of array writer visitor
     *
     * @var ArrayWriterVisitor
     */
    protected $arrayWriterVisitor;



    /**
     * Creates a new instance of array writer
     *
     * @param array $visitors
     * @return ArrayTreeWriter
     */
    public static function getInstance(array $visitors = [])
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $arrayWriterVisitor = $objectManager->get(ArrayWriterVisitor::class);
        $visitors[] = $arrayWriterVisitor;

        $arrayTreeWriter = $objectManager->get(ArrayTreeWriter::class, $visitors, $arrayWriterVisitor);
        return $arrayTreeWriter;
    }


    /**
     * Constructor for array tree writer
     *
     * @param array $visitors
     * @param ArrayWriterVisitor|TreeWalkerVisitorInterface $arrayWriterVisitor
     * @throws \Exception
     */
    public function __construct(array $visitors, TreeWalkerVisitorInterface $arrayWriterVisitor)
    {
        parent::__construct($visitors);
        $this->arrayWriterVisitor = $arrayWriterVisitor;
    }



    /**
     * Returns array of given tree
     *
     * @param TreeInterface $tree
     * @return array
     */
    public function writeTree(TreeInterface $tree)
    {
        $this->traverseTreeDfs($tree);
        $nodeArray = $this->arrayWriterVisitor->getNodeArray();
        return $nodeArray;
    }
}
