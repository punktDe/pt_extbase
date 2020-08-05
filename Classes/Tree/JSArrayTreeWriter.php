<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class JSArrayTreeWriter extends ArrayTreeWriter
{
    /**
     * Creates a new instance of array writer
     *
     * @param array $visitors
     * @return ArrayTreeWriter
     */
    public static function getInstance(array $visitors = [])
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $arrayWriterVisitor = $objectManager->get(JSArrayTreeWriterVisitor::class);
        $visitors[] = $arrayWriterVisitor;
        $jsonTreeWriter = $objectManager->get(ArrayTreeWriter::class, $visitors, $arrayWriterVisitor);
        return $jsonTreeWriter;
    }

}
