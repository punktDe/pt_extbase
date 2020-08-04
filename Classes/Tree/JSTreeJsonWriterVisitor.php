<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class JSTreeJsonWriterVisitor extends ArrayWriterVisitor
{
    /**
     * @see TreeWalkerVisitorInterface::doFirstVisit()
     *
     * @param NodeInterface $node
     * @param integer &$index Holds the visitation index of treewalker
     * @param integer &$level Holds level of visitation in tree, starting at 1
     */
    public function doFirstVisit(NodeInterface $node, &$index, &$level)
    {
        $nodeUid = $node->getUid();
        $metadata = '';


        $arrayForNode = [
            'data' => $node->getLabel(),
            'attr' => [
                'id' => $node->getUid(),
                'data-meta' => trim($metadata),
                'disabled' => !$node->isAccessible(),
            ],
            'children' => []
        ];

        $this->nodeStack->push($arrayForNode);
    }
}
