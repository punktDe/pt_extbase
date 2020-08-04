<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class JSTreeJsonTreeWriter extends JsonTreeWriter
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

        $arrayWriterVisitor = $objectManager->get(JSTreeJsonWriterVisitor::class);
        $visitors[] = $arrayWriterVisitor;
        $jsonTreeWriter = $objectManager->get(JsonTreeWriter::class, $visitors, $arrayWriterVisitor);
        return $jsonTreeWriter;
    }
}
