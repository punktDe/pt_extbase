<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class ExtJsJsonTreeWriter extends JsonTreeWriter
{
    /**
     * Creates a new instance of json writer
     *
     * @param array $visitors
     * @return JsonTreeWriter
     */
    public static function getInstance(array $visitors = [])
    {
        $arrayWriterVisitor = new ExtJsJsonWriterVisitor();
        $visitors[] = $arrayWriterVisitor;
        $jsonTreeWriter = new JsonTreeWriter($visitors, $arrayWriterVisitor);
        return $jsonTreeWriter;
    }
}
