<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class JSArrayTreeWriterVisitor extends ArrayWriterVisitor
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
        $arrayForNode = [
            'data' => $node->getLabel(),
            'attr' => [
                'id' => $node->getUid(),
                'data-meta' => '',
                'disabled' => !$node->isAccessible(),
            ],
            'children' => []
        ];

        $this->nodeStack->push($arrayForNode);
    }
}
