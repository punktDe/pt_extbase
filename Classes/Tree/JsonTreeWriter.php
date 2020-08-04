<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class JsonTreeWriter extends ArrayTreeWriter
{
    /**
     * Creates a new instance of json writer
     *
     * @param array $visitors
     * @return JsonTreeWriter
     */
    public static function getInstance(array $visitors = [])
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $arrayWriterVisitor = $objectManager->get(ArrayWriterVisitor::class);
        /** @var ArrayWriterVisitor $arrayWriterVisitor */

        $visitors[] = $arrayWriterVisitor;
        $jsonTreeWriter = $objectManager->get(JsonTreeWriter::class, $visitors, $arrayWriterVisitor);
        return $jsonTreeWriter;
    }


    /**
     * Constructor for array tree writer
     *
     * @param array $visitors
     * @param ArrayWriterVisitor|TreeWalkerVisitorInterface $arrayWriterVisitor
     */
    public function __construct(array $visitors, TreeWalkerVisitorInterface $arrayWriterVisitor)
    {
        parent::__construct($visitors, $arrayWriterVisitor);
    }


    /**
     * Returns JSON notation of given tree
     *
     * @param TreeInterface $tree
     * @return string JSON encoding of tree
     */
    public function writeTree(TreeInterface $tree)
    {
        $nodeArray = parent::writeTree($tree);

        return '[' . json_encode($nodeArray) . ']';
    }
}
